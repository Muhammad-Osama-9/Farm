# Authentication Middleware Documentation

## Overview

This Laravel application includes a custom authentication middleware that checks if users are logged in before allowing access to protected routes.

## Middleware Files

### 1. `app/Http/Middleware/Authenticate.php`

-   **Purpose**: Main authentication middleware
-   **Functionality**:
    -   Checks if user is authenticated using `Auth::check()`
    -   Redirects unauthenticated users to login page (`/`)
    -   Handles JSON requests with proper 401 responses
    -   Shows error message when redirecting

### 2. `app/Http/Middleware/CheckAuth.php`

-   **Purpose**: Alternative authentication middleware
-   **Functionality**: Simple authentication check and redirect

## Registration

The middleware is registered in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'checkauth' => \App\Http\Middleware\CheckAuth::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
    ]);
})
```

## Usage

### 1. Individual Routes

```php
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
```

### 2. Route Groups (Recommended)

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/logout', [UserController::class, 'logout']);
});
```

### 3. Global Application

To apply to all routes, add to `bootstrap/app.php`:

```php
$middleware->append(\App\Http\Middleware\Authenticate::class);
```

## Protected Routes

Currently protected routes:

-   `/dashboard` - Smart farm dashboard
-   `/home` - Main landing page
-   `/profile` - User profile management
-   `/logout` - User logout

## Public Routes

Routes that don't require authentication:

-   `/` - Login page
-   `/signup` - Registration page
-   `/register` - User registration (POST)
-   `/login` - User authentication (POST)

## How It Works

1. **Request comes in** to a protected route
2. **Middleware checks** if user is authenticated using `Auth::check()`
3. **If authenticated**: Request continues to the controller
4. **If not authenticated**: User is redirected to `/` (login page) with error message

## Error Handling

-   **Web requests**: Redirected to login page with error message
-   **API requests**: Returns JSON response with 401 status
-   **Error message**: "Please login to access this page."

## Testing

To test the middleware:

1. **Without login**: Try accessing `/dashboard` or `/profile` - should redirect to login
2. **With login**: After logging in, should be able to access all protected routes
3. **After logout**: Should be redirected to login page when accessing protected routes

## Customization

To modify the redirect behavior:

1. Edit `app/Http/Middleware/Authenticate.php`
2. Change the redirect URL in the `handle` method
3. Modify error messages as needed

## Security Features

-   Session regeneration on login
-   CSRF protection on forms
-   Password hashing with bcrypt
-   Session invalidation on logout
