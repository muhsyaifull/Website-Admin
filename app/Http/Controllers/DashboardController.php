<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $adminUsers = User::where('role', 'admin')->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'activeUsers',
            'adminUsers',
            'recentUsers'
        ));
    }
}