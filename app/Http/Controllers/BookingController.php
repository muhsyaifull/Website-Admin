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

        $monthStart = $selectedDate->copy()->startOfMonth();
        $monthEnd = $selectedDate->copy()->endOfMonth();

        $monthlyReservationCountByPackage = Booking::query()
            ->selectRaw('package_id, COUNT(*) as total_reservations')
            ->whereBetween('visit_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('package_id')
            ->pluck('total_reservations', 'package_id');

        $monthlyPackageSummary = $packages
            ->map(function ($package) use ($monthlyReservationCountByPackage) {
                return [
                    'id' => $package->id,
                    'label' => $package->label,
                    'name' => $package->name,
                    'total_reservations' => (int) ($monthlyReservationCountByPackage[$package->id] ?? 0),
                ];
            })
            ->filter(fn($item) => $item['total_reservations'] > 0)
            ->values();

        $monthlyReservationTotal = $monthlyPackageSummary->sum('total_reservations');

        $bookings = Booking::with(['package', 'user', 'bookingSessions.tour', 'bookingSessions.educator'])
            ->whereDate('visit_date', $selectedDate)
            ->when($selectedPackageId, function ($query) use ($selectedPackageId) {
                $query->where('package_id', $selectedPackageId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.bookings.index', compact(
            'bookings',
            'selectedDate',
            'packages',
            'monthStart',
            'monthEnd',
            'monthlyPackageSummary',
            'monthlyReservationTotal'
        ));
    }

    public function show(Booking $booking)
    {
        $booking->load(['package.tours', 'user', 'bookingSessions.tour', 'bookingSessions.educator']);

        return view('admin.bookings.show', compact('booking'));
    }
}
