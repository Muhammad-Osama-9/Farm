<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\SensorDataController;

// Public routes (no authentication required)
Route::get('/', function () {
    return view('home');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes (authentication required)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/home', function () {
        return view('main');
    });
    Route::post('/logout', [UserController::class, 'logout']);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'update']);

    // AI Assistant routes
    Route::get('/ai-assistant', [AiController::class, 'index']);
    Route::post('/api/ai/process', [AiController::class, 'processRequest']);
    Route::get('/api/ai/suggestions', [AiController::class, 'getSuggestions']);
});

// Sensor Data API routes (can be public for IoT devices)
Route::prefix('api/sensors')->group(function () {
    Route::post('/update', [SensorDataController::class, 'updateReadings']);
    Route::get('/readings', [SensorDataController::class, 'getReadings']);
    Route::get('/history', [SensorDataController::class, 'getHistory']);
    Route::get('/stats', [SensorDataController::class, 'getStats']);
    Route::post('/bulk-update', [SensorDataController::class, 'bulkUpdate']);
});