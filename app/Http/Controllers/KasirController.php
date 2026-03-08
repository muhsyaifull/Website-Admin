<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\TourSession;
use App\Models\Booking;
use App\Models\SessionTemplate;
use Carbon\Carbon;

class KasirController extends Controller
{
    /**
     * Display kasir dashboard
     */
    public function index()
    {
        $today = Carbon::today();

        // Auto-generate sessions for today from templates
        SessionTemplate::ensureSessionsForDate($today);

        // Get tour sessions for today from active templates
        $tamanSessions = TourSession::with('educator')
            ->where('type', 'taman')
            ->forToday()
            ->fromActiveTemplate()
            ->active()
            ->orderedByTime()
            ->get();

        $museumSessions = TourSession::with('educator')
            ->where('type', 'museum')
            ->forToday()
            ->fromActiveTemplate()
            ->active()
            ->orderedByTime()
            ->get();

        // Get today's bookings
        $todaysBookings = Booking::with(['package', 'tamanSession.educator', 'museumSession.educator'])
            ->today()
            ->confirmed()
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalBookings = $todaysBookings->count();
        $totalParticipants = $todaysBookings->sum('total_participants');
        $totalRevenue = $todaysBookings->sum('total_price');

        return view('kasir.index', compact(
            'tamanSessions',
            'museumSessions',
            'todaysBookings',
            'totalBookings',
            'totalParticipants',
            'totalRevenue'
        ));
    }

    /**
     * Show booking form
     */
    public function createBooking()
    {
        // Auto-generate sessions for today from templates
        SessionTemplate::ensureSessionsForDate(Carbon::today());

        $packages = Package::active()->get();
        $tamanSessions = TourSession::with('educator')
            ->where('type', 'taman')
            ->forToday()
            ->fromActiveTemplate()
            ->active()
            ->orderedByTime()
            ->get();

        $museumSessions = TourSession::with('educator')
            ->where('type', 'museum')
            ->forToday()
            ->fromActiveTemplate()
            ->active()
            ->orderedByTime()
            ->get();

        return view('kasir.booking.create', compact('packages', 'tamanSessions', 'museumSessions'));
    }

    /**
     * Store new booking
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'representative_name' => 'required|string|max:100',
            'representative_address' => 'required|string',
            'representative_phone' => 'required|string|max:20',
            'adult_count' => 'required|integer|min:1',
            'child_count' => 'integer|min:0',
            'taman_session_id' => 'required|exists:tour_sessions,id',
            'museum_session_id' => 'required|exists:tour_sessions,id',
            'visit_date' => 'required|date|after_or_equal:today',
        ]);

        $package = Package::findOrFail($request->package_id);
        $tamanSession = TourSession::findOrFail($request->taman_session_id);
        $museumSession = TourSession::findOrFail($request->museum_session_id);

        $adultCount = $request->adult_count;
        $childCount = $request->child_count ?? 0;
        $totalParticipants = $adultCount + $childCount;

        // Check capacity
        if (!$tamanSession->canAccommodate($totalParticipants)) {
            return back()->withErrors(['taman_session_id' => 'Taman session capacity is insufficient']);
        }

        if (!$museumSession->canAccommodate($totalParticipants)) {
            return back()->withErrors(['museum_session_id' => 'Museum session capacity is insufficient']);
        }

        // Calculate price (only adults are charged)
        $unitPrice = $package->price;
        $totalPrice = $unitPrice * $adultCount;

        // Create booking
        $booking = Booking::create([
            'package_id' => $package->id,
            'user_id' => auth()->id(),
            'representative_name' => $request->representative_name,
            'representative_address' => $request->representative_address,
            'representative_phone' => $request->representative_phone,
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'total_participants' => $totalParticipants,
            'taman_session_id' => $tamanSession->id,
            'museum_session_id' => $museumSession->id,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'visit_date' => $request->visit_date,
            'status' => 'confirmed',
        ]);

        // Update session booking counts
        $tamanSession->increment('booked', $totalParticipants);
        $museumSession->increment('booked', $totalParticipants);

        return redirect()->route('kasir.booking.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        $booking->load(['package', 'user', 'tamanSession.educator', 'museumSession.educator']);

        return view('kasir.booking.show', compact('booking'));
    }

    /**
     * Get session data for AJAX requests
     */
    public function getSessionData($type)
    {
        $sessions = TourSession::with('educator')
            ->where('type', $type)
            ->active()
            ->orderedByTime()
            ->get();

        return response()->json($sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'time' => $session->start_time->format('H:i'),
                'label' => $session->label,
                'capacity' => $session->capacity,
                'booked' => $session->booked,
                'available' => $session->available,
                'guide' => $session->educator->name,
                'is_full' => $session->is_full,
                'is_low' => $session->is_low,
                'status' => $session->status,
                'bar_color' => $session->bar_color,
                'status_background' => $session->status_background,
                'status_color' => $session->status_color,
                'booking_percentage' => $session->booking_percentage,
            ];
        }));
    }
}