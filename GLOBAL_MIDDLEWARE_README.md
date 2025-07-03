# Global Authentication Middleware

## Overview

The Global Authentication Middleware automatically checks authentication on every request to your Laravel application. It runs globally and protects all routes except those explicitly marked as public.

## 📁 Files Location

### Core Files:

-   **`app/Http/Middleware/GlobalAuth.php`** - Main middleware logic
-   **`config/auth_routes.php`** - Configuration file for routes and settings
-   **`bootstrap/app.php`** - Global middleware registration

## ⚙️ Configuration

### 1. Public Routes (`config/auth_routes.php`)

Routes that don't require authentication:

```php
'public_routes' => [
    '/',                    // Login page
    '/signup',              // Registration page
    '/register',            // Registration POST
    '/login',               // Login POST
    'api/auth/*',           // API authentication endpoints
    'health',               // Health check
    'up',                   // Laravel up route
],
```

### 2. Protected Routes (`config/auth_routes.php`)

Routes that always require authentication:

```php
'protected_routes' => [
    '/dashboard',
    '/home',
    '/profile',
    '/logout',
    'api/*',                // All API routes except auth
],
```

### 3. Settings (`config/auth_routes.php`)

Authentication behavior configuration:

```php
'settings' => [
    'login_redirect' => '/',                    // Redirect URL
    'error_message' => 'Please login to access this page.',
    'enabled' => true,                          // Enable/disable middleware
    'debug' => false,                           // Debug mode
],
```

## 🔧 How It Works

### 1. **Automatic Execution**

The middleware runs on every HTTP request automatically.

### 2. **Route Checking**

-   Checks if the current route is in the public routes list
-   If public → allows access
-   If protected → checks authentication

### 3. **Authentication Check**

-   Uses `Auth::check()` to verify if user is logged in
-   If authenticated → allows access
-   If not authenticated → redirects to login

### 4. **Response Handling**

-   **Web requests**: Redirects to login page with error message
-   **API requests**: Returns JSON response with 401 status

## 📋 Current Setup

### Protected Routes (Require Login):

-   `/dashboard` - Smart farm dashboard
-   `/home` - Main landing page
-   `/profile` - User profile management
-   `/logout` - User logout

### Public Routes (No Login Required):

-   `/` - Login page
-   `/signup` - Registration page
-   `/register` - User registration (POST)
-   `/login` - User authentication (POST)

## 🚀 Usage Examples

### Adding New Public Routes:

Edit `config/auth_routes.php`:

```php
'public_routes' => [
    // ... existing routes
    '/about',               // About page
    '/contact',             // Contact page
    'docs/*',               // Documentation pages
],
```

### Adding New Protected Routes:

```php
'protected_routes' => [
    // ... existing routes
    '/admin/*',             // Admin panel
    '/user/settings',       // User settings
],
```

### Pattern Matching:

-   `'api/*'` - All routes starting with 'api/'
-   `'admin/*'` - All routes starting with 'admin/'
-   `'user/*'` - All routes starting with 'user/'

## 🔍 Testing

### Test Public Routes:

```bash
# These should work without login
curl http://localhost:8000/
curl http://localhost:8000/signup
```

### Test Protected Routes:

```bash
# These should redirect to login without authentication
curl http://localhost:8000/dashboard
curl http://localhost:8000/profile
```

## 🛠️ Customization

### Change Redirect URL:

```php
// config/auth_routes.php
'settings' => [
    'login_redirect' => '/login',  // Change from '/' to '/login'
],
```

### Change Error Message:

```php
'settings' => [
    'error_message' => 'You must be logged in to view this page.',
],
```

### Disable Global Authentication:

```php
'settings' => [
    'enabled' => false,
],
```

## 📝 Benefits

1. **Automatic Protection** - No need to manually add middleware to each route
2. **Centralized Configuration** - All auth settings in one place
3. **Flexible** - Easy to add/remove public/protected routes
4. **Pattern Support** - Use wildcards for route groups
5. **API Ready** - Handles both web and API requests
6. **Configurable** - Customize messages, redirects, and behavior

## 🔒 Security Features

-   **Session-based authentication** using Laravel's Auth system
-   **CSRF protection** on forms
-   **Secure redirects** with proper error messages
-   **API authentication** with proper JSON responses
-   **Pattern-based route matching** for flexible protection

The global middleware ensures that your entire application is protected by default, with only explicitly defined public routes being accessible without authentication!
