<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\RoleEnum;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is inactive.');
        }

        $validRoles = array_column(RoleEnum::cases(), 'value');
        if (!in_array($user->role, $validRoles)) {
            abort(403, 'Invalid role.');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'You do not have access to this page.');
        }

        return $next($request);
    }
}