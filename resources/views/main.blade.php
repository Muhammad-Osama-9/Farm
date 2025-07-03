@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Modern Header -->
<header class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-lg">🌾</span>
                </div>
                <span
                    class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    Farm System
                </span>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="#"
                    class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Home</a>
                <a href="#"
                    class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Features</a>
                <a href="#"
                    class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Contact</a>
                @auth
                    <a href="/dashboard"
                        class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Dashboard</a>
                    <a href="/ai-assistant"
                        class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 flex items-center">
                        <span class="mr-1">🤖</span>
                        AI Assistant
                    </a>
                    <a href="/profile"
                        class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Profile</a>
                @else
                    <a href="/"
                        class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Login</a>
                    <a href="/signup"
                        class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Register</a>
                @endauth
            </nav>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200/50 backdrop-blur-xl">
                            <div class="p-4">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <a href="/dashboard"
                                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        <span>Dashboard</span>
                                    </a>
                                    <a href="/ai-assistant"
                                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <span class="text-lg">🤖</span>
                                        <span>AI Assistant</span>
                                    </a>
                                    <a href="/profile"
                                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <span>Profile</span>
                                    </a>
                                    <hr class="my-2">
                                    <form method="POST" action="/logout">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center space-x-3 p-2 rounded-lg hover:bg-red-50 text-red-600 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            <span>Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="/"
                        class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">Login</a>
                    <a href="/signup"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-20 px-4 overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
    <div class="absolute top-0 left-0 w-72 h-72 bg-blue-400/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-72 h-72 bg-purple-400/20 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto text-center">
        <h1 class="text-5xl md:text-6xl font-bold mb-6">
            <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Grow with Confidence
            </span>
        </h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8 leading-relaxed">
            Manage your farm efficiently and effectively with our intuitive Farm Management System.
            Monitor crops, track livestock, and make data-driven decisions.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="/dashboard"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Go to Dashboard
                </a>
            @else
                <a href="/signup"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                    Get Started Free
                </a>
                <a href="/"
                    class="inline-flex items-center px-8 py-4 border-2 border-blue-500 text-blue-600 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-200">
                    Sign In
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Powerful Features</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Everything you need to manage your farm efficiently
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl border border-blue-100 hover:shadow-xl transition-all duration-300">
                <div
                    class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Crop Monitoring</h3>
                <p class="text-gray-600 leading-relaxed">
                    Monitor crop growth, track conditions, and receive insights to improve your yield with our built-in
                    tools.
                </p>
            </div>

            <!-- Feature 2 -->
            <div
                class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl border border-purple-100 hover:shadow-xl transition-all duration-300">
                <div
                    class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Livestock Tracking</h3>
                <p class="text-gray-600 leading-relaxed">
                    Manage livestock inventory, health records, and movements in one convenient place.
                </p>
            </div>

            <!-- Feature 3 -->
            <div
                class="bg-gradient-to-br from-green-50 to-emerald-50 p-8 rounded-2xl border border-green-100 hover:shadow-xl transition-all duration-300">
                <div
                    class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Smart Analytics</h3>
                <p class="text-gray-600 leading-relaxed">
                    Get access to real-time data and reports to help you make informed decisions for your farm.
                </p>
            </div>

            <!-- Feature 4 - AI Assistant -->
            <div
                class="bg-gradient-to-br from-orange-50 to-red-50 p-8 rounded-2xl border border-orange-100 hover:shadow-xl transition-all duration-300">
                <div
                    class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center mb-6">
                    <span class="text-white text-xl">🤖</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">AI Farm Assistant</h3>
                <p class="text-gray-600 leading-relaxed">
                    Get expert advice on crop management, soil health, pest control, and sustainable farming practices
                    with our AI-powered assistant.
                </p>
                @auth
                <a href="/ai-assistant"
                    class="inline-flex items-center mt-4 text-orange-600 hover:text-orange-700 font-medium">
                    Try AI Assistant
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 px-4 bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="max-w-4xl mx-auto text-center">
        <h3 class="text-3xl font-bold text-white mb-4">Ready to Transform Your Farm?</h3>
        <p class="text-xl text-blue-100 mb-8">
            Join thousands of farmers who are already using our system to improve their operations.
        </p>
        @auth
            <a href="/dashboard"
                class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-xl font-semibold hover:bg-gray-50 transform hover:scale-105 transition-all duration-200 shadow-lg">
                Access Dashboard
            </a>
        @else
            <a href="/signup"
                class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-xl font-semibold hover:bg-gray-50 transform hover:scale-105 transition-all duration-200 shadow-lg">
                Start Free Trial
            </a>
        @endauth
    </div>
</section>
@endsection