<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\TourSession;
use App\Models\Booking;
use App\Models\SessionTemplate;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashierBookingController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        SessionTemplate::ensureSessionsForDate($today);

        $tours = Tour::active()->ordered()->get();
        $tourSessions = [];
        foreach ($tours as $tour) {
            $tourSessions[$tour->id] = TourSession::with('educator')
                ->where('tour_id', $tour->id)
                ->forToday()
                ->fromActiveTemplate()
                ->active()
                ->orderedByTime()
                ->get();
        }

        $todaysBookings = Booking::with(['package', 'bookingSessions.tour', 'bookingSessions.educator'])
            ->today()
            ->confirmed()
            ->orderBy('created_at', 'desc')
            ->get();

        $totalBookings = $todaysBookings->count();
        $totalParticipants = $todaysBookings->sum('total_participants');
        $totalRevenue = $todaysBookings->sum('total_price');

        return view('kasir.index', compact(
            'tours',
            'tourSessions',
            'todaysBookings',
            'totalBookings',
            'totalParticipants',
            'totalRevenue'
        ));
    }

    public function create()
    {
        SessionTemplate::ensureSessionsForDate(Carbon::today());

        $packages = Package::with('tours')->active()->get();
        $packageTourIds = $packages
            ->mapWithKeys(function ($package) {
                return [
                    $package->id => $package->tours
                        ->where('is_active', true)
                        ->pluck('id')
                        ->values()
                        ->toArray()
                ];
            });

        $tours = Tour::active()->ordered()->get();
        $tourSessions = [];
        foreach ($tours as $tour) {
            $tourSessions[$tour->id] = TourSession::with('educator')
                ->where('tour_id', $tour->id)
                ->forToday()
                ->fromActiveTemplate()
                ->active()
                ->orderedByTime()
                ->get();
        }

        $visitorTypes = Booking::VISITOR_TYPES;

        return view('kasir.booking.create', compact(
            'packages',
            'packageTourIds',
            'tours',
            'tourSessions',
            'visitorTypes'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'package_id' => 'required|exists:packages,id',
            'representative_name' => 'required|string|max:100',
            'representative_address' => 'required|string',
            'representative_phone' => 'required|string|max:20',
            'visitor_type' => 'required|in:' . implode(',', array_keys(Booking::VISITOR_TYPES)),
            'adult_count' => 'required|integer|min:1',
            'child_count' => 'integer|min:0',
            'infant_count' => 'integer|min:0',
            'visit_date' => 'required|date|after_or_equal:today',
        ];

        $package = Package::with('tours')->findOrFail($request->package_id);
        $activeTours = $package->tours->where('is_active', true);

        foreach ($activeTours as $tour) {
            $rules['tour_session_' . $tour->id] = 'required|exists:tour_sessions,id';
        }

        $request->validate($rules);

        $adultCount = (int) $request->adult_count;
        $childCount = (int) ($request->child_count ?? 0);
        $infantCount = (int) ($request->infant_count ?? 0);
        $totalParticipants = $adultCount + $childCount + $infantCount;
        $totalPrice = ($adultCount + $childCount) * $package->price;

        $selectedSessionIds = [];
        foreach ($activeTours as $tour) {
            $selectedSessionIds[$tour->id] = (int) $request->input('tour_session_' . $tour->id);
        }

        $allowedTourIds = $activeTours->pluck('id')->map(fn($id) => (int) $id)->all();
        foreach ($request->all() as $key => $value) {
            if (!str_starts_with($key, 'tour_session_') || blank($value)) {
                continue;
            }
            $tourId = (int) str_replace('tour_session_', '', $key);
            if (!in_array($tourId, $allowedTourIds, true)) {
                throw ValidationException::withMessages([
                    $key => 'Tour yang dipilih tidak termasuk dalam package.',
                ]);
            }
        }

        $this->validateSessionGap($selectedSessionIds);

        $bookingData = [
            'package_id' => $package->id,
            'user_id' => auth()->id(),
            'representative_name' => $request->representative_name,
            'representative_address' => $request->representative_address,
            'representative_phone' => $request->representative_phone,
            'visitor_type' => $request->visitor_type,
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'infant_count' => $infantCount,
            'total_participants' => $totalParticipants,
            'unit_price' => $package->price,
            'total_price' => $totalPrice,
            'visit_date' => $request->visit_date,
            'status' => 'confirmed',
        ];

        $booking = null;
        DB::transaction(function () use ($bookingData, $selectedSessionIds, $activeTours, $request, $totalParticipants, &$booking) {
            $booking = Booking::create($bookingData);

            foreach ($activeTours as $tour) {
                $sessionId = $selectedSessionIds[$tour->id];

                $updatedRows = TourSession::query()
                    ->where('id', $sessionId)
                    ->where('tour_id', $tour->id)
                    ->whereDate('date', $request->visit_date)
                    ->where('is_active', true)
                    ->whereRaw('booked + ? <= capacity', [$totalParticipants])
                    ->update([
                        'booked' => DB::raw('booked + ' . $totalParticipants),
                    ]);

                if ($updatedRows === 0) {
                    throw ValidationException::withMessages([
                        'tour_session_' . $tour->id => 'Sesi ' . $tour->name . ' tidak tersedia atau kapasitas tidak mencukupi.',
                    ]);
                }

                $booking->bookingSessions()->attach($sessionId, ['tour_id' => $tour->id]);
            }
        });

        return redirect()->route('kasir.booking.show', $booking)
            ->with('success', 'Booking berhasil dibuat!');
    }

    public function show(Booking $booking)
    {
        $booking->load(['package.tours', 'user', 'bookingSessions.educator', 'bookingSessions.tour']);

        $tourSessionMap = [];
        foreach ($booking->bookingSessions as $session) {
            $tourSessionMap[$session->pivot->tour_id] = $session;
        }

        return view('kasir.booking.show', compact('booking', 'tourSessionMap'));
    }

    public function getSessionData($tourId)
    {
        SessionTemplate::ensureSessionsForDate(Carbon::today());

        $sessions = TourSession::with('educator')
            ->where('tour_id', $tourId)
            ->forToday()
            ->fromActiveTemplate()
            ->active()
            ->orderedByTime()
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'label' => $session->label,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'capacity' => $session->capacity,
                    'booked' => $session->booked,
                    'available' => $session->capacity - $session->booked,
                    'educator' => $session->educator ? $session->educator->name : 'TBD',
                ];
            });

        return response()->json($sessions);
    }

    private function validateSessionGap(array $selectedSessionIds): void
    {
        if (count($selectedSessionIds) < 2) {
            return;
        }

        $sessions = TourSession::whereIn('id', array_values($selectedSessionIds))
            ->get(['id', 'start_time', 'end_time'])
            ->keyBy('id');

        $sorted = collect(array_values($selectedSessionIds))
            ->map(fn($id) => $sessions[$id] ?? null)
            ->filter()
            ->sortBy(fn($s) => $s->start_time)
            ->values();

        for ($i = 0; $i < $sorted->count() - 1; $i++) {
            $current = $sorted[$i];
            $next = $sorted[$i + 1];
            $endTime = Carbon::parse($current->end_time);
            $startTime = Carbon::parse($next->start_time);

            $gapMinutes = $endTime->diffInMinutes($startTime, false);

            if ($gapMinutes < 60) {
                throw ValidationException::withMessages([
                    'session_gap' => 'Jeda antar sesi tour minimal 60 menit. Harap pilih sesi dengan selisih waktu yang cukup.',
                ]);
            }
        }
    }
}