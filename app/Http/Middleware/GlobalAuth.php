<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Global Authentication Middleware
 * 
 * This middleware runs on every request and automatically checks authentication.
 * It protects all routes except those explicitly marked as public.
 * 
 * Features:
 * - Automatic route protection
 * - Configurable public routes
 * - Pattern-based route matching
 * - API and web request handling
 */
class GlobalAuth
{
    /**
     * Get public routes from configuration
     */
    protected function getPublicRoutes(): array
    {
        return config('auth_routes.public_routes', []);
    }

    /**
     * Get protected routes from configuration
     */
    protected function getProtectedRoutes(): array
    {
        return config('auth_routes.protected_routes', []);
    }

    /**
     * Get authentication settings
     */
    protected function getSettings(): array
    {
        return config('auth_routes.settings', []);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentPath = $request->path();

        // Check if current route is public
        if ($this->isPublicRoute($currentPath)) {
            return $next($request);
        }

        // Check if user is authenticated for protected routes
        if (!Auth::check()) {
            return $this->handleUnauthenticated($request);
        }

        // Continue with the request
        return $next($request);
    }

    /**
     * Check if the given path is a public route
     */
    protected function isPublicRoute(string $path): bool
    {
        $publicRoutes = $this->getPublicRoutes();

        // Check exact matches
        if (in_array('/' . $path, $publicRoutes) || in_array($path, $publicRoutes)) {
            return true;
        }

        // Check pattern matches (e.g., 'api/auth/*')
        foreach ($publicRoutes as $pattern) {
            if ($this->matchesPattern($path, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if path matches a pattern (supports wildcards)
     */
    protected function matchesPattern(string $path, string $pattern): bool
    {
        // Convert pattern to regex
        $regex = str_replace('*', '.*', $pattern);
        $regex = str_replace('/', '\/', $regex);

        return preg_match('/^' . $regex . '$/', $path);
    }

    /**
     * Handle unauthenticated requests
     */
    protected function handleUnauthenticated(Request $request): Response
    {
        $settings = $this->getSettings();

        // For API requests, return JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            $apiResponse = $settings['api_response'] ?? [
                'message' => 'Authentication required.',
                'status' => 'unauthorized',
                'code' => 401
            ];

            return response()->json([
                'message' => $apiResponse['message'],
                'redirect' => $settings['login_redirect'] ?? '/',
                'status' => $apiResponse['status']
            ], $apiResponse['code']);
        }

        // For web requests, redirect to login with error message
        return redirect($settings['login_redirect'] ?? '/')
            ->with('error', $settings['error_message'] ?? 'Please login to access this page.');
    }
}