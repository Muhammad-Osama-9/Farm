<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// All routes - GlobalAuth middleware automatically protects them
Route::get('/', function () {
    return view('home');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Dashboard routes (automatically protected by GlobalAuth middleware)
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/home', function () {
    return view('main');
});
Route::post('/logout', [UserController::class, 'logout']);

// Profile routes (automatically protected by GlobalAuth middleware)
Route::get('/profile', [ProfileController::class, 'index']);
Route::post('/profile', [ProfileController::class, 'update']);