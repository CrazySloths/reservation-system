<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is logged in via session
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Get user role from session
        $userRole = session('user_role', 'citizen');

        // Normalize role for comparison (lowercase)
        $userRole = strtolower($userRole);

        // Check if user has any of the allowed roles
        foreach ($roles as $role) {
            if (strtolower($role) === $userRole || str_contains(strtolower($userRole), strtolower($role))) {
                return $next($request);
            }
        }

        // Unauthorized - redirect to their own dashboard
        switch ($userRole) {
            case 'super admin':
                return redirect()->route('superadmin.dashboard')->with('error', 'Unauthorized access.');
            case 'admin':
                return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.');
            case 'reservations staff':
                return redirect()->route('staff.dashboard')->with('error', 'Unauthorized access.');
            default:
                return redirect()->route('citizen.dashboard')->with('error', 'Unauthorized access.');
        }
    }
}

