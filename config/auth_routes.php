<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public Routes Configuration
    |--------------------------------------------------------------------------
    |
    | These routes are accessible without authentication.
    | You can use exact paths or patterns with wildcards (*).
    |
    */
    'public_routes' => [
        // Authentication routes
        '/',                    // Login page
        '/signup',              // Registration page
        '/register',            // Registration POST
        '/login',               // Login POST

        // API authentication routes
        'api/auth/*',           // API authentication endpoints

        // System routes
        'health',               // Health check
        'up',                   // Laravel up route

        // Add more public routes here
        // 'public/*',           // All routes starting with 'public'
        // 'docs/*',             // Documentation routes
    ],

    /*
    |--------------------------------------------------------------------------
    | Protected Routes Configuration
    |--------------------------------------------------------------------------
    |
    | These routes always require authentication.
    | If empty, all routes except public routes require auth.
    |
    */
    'protected_routes' => [
        // Dashboard and main pages
        '/dashboard',
        '/home',
        '/profile',
        '/logout',

        // API routes (except auth)
        'api/*',

        // Add more protected routes here
        // 'admin/*',            // Admin panel routes
        // 'user/*',             // User-specific routes
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Configure authentication behavior
    |
    */
    'settings' => [
        // Redirect URL for unauthenticated users
        'login_redirect' => '/',

        // Error message for unauthenticated users
        'error_message' => 'Please login to access this page.',

        // API response settings
        'api_response' => [
            'message' => 'Authentication required.',
            'status' => 'unauthorized',
            'code' => 401
        ],

        // Enable/disable global authentication
        'enabled' => true,

        // Debug mode (logs authentication checks)
        'debug' => false,
    ],
];