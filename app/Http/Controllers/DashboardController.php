<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Package;
use App\Models\Booking;
use App\Models\Educator;
use App\Models\TourSession;
use App\Models\SessionTemplate;
use App\Models\Tour;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the dashboard based on user role
     */
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'cashier':
                return redirect()->route('kasir.index');
            case 'educator':
                return $this->educatorDashboard();
            case 'admin':
                return $this->adminDashboard();
            default:
                return $this->adminDashboard();
        }
    }

    private function adminDashboard()
    {
        $this->ensureSessionsOnce();

        $stats = cache()->remember('admin_dashboard_stats', 300, function () {
            return [
                'totalUsers' => User::count(),
                'activeUsers' => User::where('is_active', true)->count(),
                'totalBookings' => Booking::count(),
                'totalRevenue' => Booking::sum('total_price'),
            ];
        });

        $todayStats = cache()->remember(
            'admin_dashboard_today_' . Carbon::today()->toDateString(),
            60,
            function () {
                return [
                    'todaysBookings' => Booking::today()->count(),
                    'todaysRevenue' => Booking::today()->sum('total_price'),
                ];
            }
        );

        $recentBookings = Booking::with(['package', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.index', array_merge(
            $stats,
            $todayStats,
            compact('recentBookings', 'recentUsers')
        ));
    }

    private function educatorDashboard()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        $this->ensureSessionsOnce();

        $tours = Tour::active()->ordered()->get();
        $tourSessions = [];
        $allSessions = collect();

        foreach ($tours as $tour) {
            $sessions = TourSession::with(['educator'])
                ->where('tour_id', $tour->id)
                ->forToday()
                ->fromActiveTemplate()
                ->active()
                ->orderedByTime()
                ->get();
            $tourSessions[$tour->id] = $sessions;
            $allSessions = $allSessions->merge($sessions);
        }

        $upcomingSessions = $allSessions->filter(function ($session) {
            return $session->isUpcoming();
        });

        $todaysBookings = Booking::today()->confirmed()->get();
        $totalVisitors = $todaysBookings->sum('total_participants');
        $totalRevenue = $todaysBookings->sum('total_price');

        return view('educator.index', compact(
            'tours',
            'tourSessions',
            'upcomingSessions',
            'totalVisitors',
            'totalRevenue'
        ));
    }

    /**
     * Global search
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|min:3|max:100',
        ]);

        $q = trim($request->input('q', ''));
        $results = [];

        if (strlen($q) < 3) {
            return view('search', compact('q', 'results'));
        }

        $user = auth()->user();
        $cacheKey = "search:{$user->role}:" . md5($q);

        $results = cache()->remember($cacheKey, 60, function () use ($q, $user) {
            $data = [];

            $data['packages'] = Package::where('name', 'like', "%{$q}%")
                ->orWhere('label', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->limit(10)->get();

            $data['educators'] = Educator::with('tours')
                ->where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->limit(10)->get();

            $data['bookings'] = Booking::where('booking_code', 'like', "%{$q}%")
                ->orWhere('representative_name', 'like', "%{$q}%")
                ->orWhere('representative_phone', 'like', "%{$q}%")
                ->limit(10)->get();

            if ($user->role === 'admin') {
                $data['users'] = User::where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->limit(10)->get();
            }

            return $data;
        });

        return view('search', compact('q', 'results'));
    }

    private function ensureSessionsOnce(): void
    {
        $cacheKey = 'sessions_ensured_' . Carbon::today()->toDateString();

        if (!cache()->has($cacheKey)) {
            SessionTemplate::ensureSessionsForDate(Carbon::today());
            cache()->put($cacheKey, true, Carbon::tomorrow());
        }
    }
}