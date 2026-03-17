<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
        ]);

        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::today();

        $bookings = Booking::with(['package', 'user', 'bookingSessions.tour', 'bookingSessions.educator'])
            ->whereDate('visit_date', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.bookings.index', compact('bookings', 'selectedDate'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['package.tours', 'user', 'bookingSessions.tour', 'bookingSessions.educator']);

        return view('admin.bookings.show', compact('booking'));
    }
}
