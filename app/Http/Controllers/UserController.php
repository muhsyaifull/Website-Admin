<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount([
            'bookings as bookings_count' => function ($query) {
            }
        ])->orderBy('created_at', 'desc')->get();

        $recentUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.users.index', compact('users', 'recentUsers'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|max:255|confirmed',
            'role' => 'required|in:cashier,educator,admin',
            'status' => 'required|boolean',
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->is_active = $request->status;

        $user->save();

        return redirect()->route('panel.users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load('bookings.package');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:cashier,educator,admin',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|max:255',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->role = $request->role;
        $user->is_active = $request->has('is_active');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('panel.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->bookings()->count() > 0) {
            return redirect()->route('panel.users.index')
                ->with('error', 'Cannot delete user that has bookings!');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('panel.users.index')
                ->with('error', 'Cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('panel.users.index')
            ->with('success', 'User deleted successfully!');
    }
}
