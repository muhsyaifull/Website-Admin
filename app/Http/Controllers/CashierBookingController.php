<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\TourSession;
use App\Models\Booking;
use App\Models\SessionTemplate;
use App\Models\Tour;
use Carbon\Carbon;

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

        $todaysBookings = Booking::with(['package', 'tamanSession.educator', 'museumSession.educator', 'bookingSessions.tour'])
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

        return view('kasir.booking.create', compact('packages', 'tours', 'tourSessions'));
    }

    public function store(Request $request)
    {
        $rules = [
            'package_id' => 'required|exists:packages,id',
            'representative_name' => 'required|string|max:100',
            'representative_address' => 'required|string',
            'representative_phone' => 'required|string|max:20',
            'adult_count' => 'required|integer|min:1',
            'child_count' => 'integer|min:0',
            'visit_date' => 'required|date|after_or_equal:today',
        ];

        $package = Package::with('tours')->findOrFail($request->package_id);

        foreach ($package->tours as $tour) {
            $rules['tour_session_' . $tour->id] = 'required|exists:tour_sessions,id';
        }

        $request->validate($rules);

        $adultCount = $request->adult_count;
        $childCount = $request->child_count ?? 0;
        $totalParticipants = $adultCount + $childCount;

        $selectedSessions = [];
        foreach ($package->tours as $tour) {
            $session = TourSession::findOrFail($request->input('tour_session_' . $tour->id));
            if (!$session->canAccommodate($totalParticipants)) {
                return back()->withErrors(['tour_session_' . $tour->id => 'Sesi ' . $tour->name . ' kapasitas tidak mencukupi']);
            }
            $selectedSessions[$tour->id] = $session;
        }

        $unitPrice = $package->price;
        $totalPrice = $unitPrice * $adultCount;

        $bookingData = [
            'package_id' => $package->id,
            'user_id' => auth()->id(),
            'representative_name' => $request->representative_name,
            'representative_address' => $request->representative_address,
            'representative_phone' => $request->representative_phone,
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'total_participants' => $totalParticipants,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'visit_date' => $request->visit_date,
            'status' => 'confirmed',
        ];

        foreach ($selectedSessions as $tourId => $session) {
            $tour = $package->tours->find($tourId);
            if ($tour && $tour->slug === 'taman') {
                $bookingData['taman_session_id'] = $session->id;
            } elseif ($tour && $tour->slug === 'museum') {
                $bookingData['museum_session_id'] = $session->id;
            }
        }

        $booking = Booking::create($bookingData);

        foreach ($selectedSessions as $tourId => $session) {
            $booking->bookingSessions()->attach($session->id, ['tour_id' => $tourId]);
            $session->increment('booked', $totalParticipants);
        }

        return redirect()->route('kasir.booking.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    public function show(Booking $booking)
    {
        $booking->load(['package.tours', 'user', 'tamanSession.educator', 'museumSession.educator', 'bookingSessions.educator', 'bookingSessions.tour']);

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
}
