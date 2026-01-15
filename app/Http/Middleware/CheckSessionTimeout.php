<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if session exists
        if (!session()->has('user_id')) {
            // Clear everything
            session()->flush();
            
            // If AJAX request, return 401
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Session expired',
                    'redirect' => route('login')
                ], 401);
            }
            
            // Regular request - redirect to login
            return redirect()->route('login')
                ->with('error', 'Your session has expired due to inactivity. Please login again.');
        }

        // Update last activity timestamp
        session()->put('last_activity', time());

        return $next($request);
    }
}

