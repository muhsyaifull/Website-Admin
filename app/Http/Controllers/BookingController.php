<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Package;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
            'package_id' => 'nullable|exists:packages,id',
        ]);

        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::today();

        $selectedPackageId = $request->input('package_id');

        $packages = Package::orderBy('label')
            ->orderBy('name')
            ->get();

        $bookings = Booking::with(['package', 'user', 'bookingSessions.tour', 'bookingSessions.educator'])
            ->whereDate('visit_date', $selectedDate)
            ->when($selectedPackageId, function ($query) use ($selectedPackageId) {
                $query->where('package_id', $selectedPackageId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.bookings.index', compact('bookings', 'selectedDate', 'packages'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['package.tours', 'user', 'bookingSessions.tour', 'bookingSessions.educator']);

        return view('admin.bookings.show', compact('booking'));
    }
}
