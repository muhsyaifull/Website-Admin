<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Package;
use App\Models\Booking;
use App\Models\Educator;
use App\Models\TourSession;

class DashboardController extends Controller
{
    /**
     * Show the dashboard based on user role
     */
    public function index()
    {
        $user = auth()->user();

        // Redirect to appropriate dashboard based on role
        switch ($user->role) {
            case 'cashier':
                return redirect()->route('kasir.index');

            case 'educator':
                return redirect()->route('educator.index');

            case 'admin':
                return redirect()->route('admin.index');

            default:
                // Fallback for any undefined roles
                return $this->showGenericDashboard();
        }
    }

    /**
     * Show generic dashboard (fallback)
     */
    private function showGenericDashboard()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $adminUsers = User::where('role', 'admin')->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'activeUsers',
            'adminUsers',
            'recentUsers'
        ));
    }

    /**
     * Global search
     */
    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $results = [];

        if (strlen($q) < 2) {
            return view('search', compact('q', 'results'));
        }

        $user = auth()->user();

        // All roles can search packages
        $results['packages'] = Package::where('name', 'like', "%{$q}%")
            ->orWhere('label', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->limit(10)->get();

        // All roles can search educators
        $results['educators'] = Educator::where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(10)->get();

        // All roles can search bookings
        $results['bookings'] = Booking::where('booking_code', 'like', "%{$q}%")
            ->orWhere('representative_name', 'like', "%{$q}%")
            ->orWhere('representative_phone', 'like', "%{$q}%")
            ->limit(10)->get();

        // Only admin can search users
        if ($user->role === 'admin') {
            $results['users'] = User::where('name', 'like', "%{$q}%")
                ->orWhere('username', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->limit(10)->get();
        }

        return view('search', compact('q', 'results'));
    }
}