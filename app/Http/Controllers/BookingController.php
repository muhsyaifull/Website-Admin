<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['package', 'user', 'bookingSessions.tour', 'bookingSessions.educator'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['package.tours', 'user', 'bookingSessions.tour', 'bookingSessions.educator']);

        return view('admin.bookings.show', compact('booking'));
    }
}
