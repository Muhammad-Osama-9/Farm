<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentication Middleware
 * 
 * This middleware checks if a user is authenticated before allowing access to protected routes.
 * If the user is not authenticated, they will be redirected to the login page.
 * 
 * Usage:
 * - Apply to individual routes: Route::get('/dashboard')->middleware('auth');
 * - Apply to route groups: Route::middleware(['auth'])->group(function () { ... });
 * - Apply globally in bootstrap/app.php
 */
class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Store the intended URL to redirect back after login
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Redirect to login page with intended URL
            return redirect('/')->with('error', 'Please login to access this page.');
        }

        // If authenticated, continue with the request
        return $next($request);
    }
}