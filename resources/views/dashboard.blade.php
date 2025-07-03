@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/home') }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Home
                        </a>
                        <div>
                            <h1
                                class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                Smart Farm Dashboard
                            </h1>
                            <p class="text-gray-600 mt-1">Monitor your farm's vital signs in real-time</p>
                        </div>
                    </div>

                    <!-- Status indicator -->
                    <div class="flex items-center space-x-2 px-4 py-2 bg-green-50 border border-green-200 rounded-xl">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-green-700">System Online</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Sensor Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Temperature Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-500 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">🌡️</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-600">Temperature</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $temperature }}°C</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-red-500 to-orange-500 h-2 rounded-full w-3/4"></div>
                    </div>
                </div>

                <!-- Humidity Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">💧</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-600">Humidity</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $humidity }}%</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full w-3/5"></div>
                    </div>
                </div>

                <!-- Water Level Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">🌊</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-600">Water Level</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $waterLevel }} cm</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-2 rounded-full w-1/2"></div>
                    </div>
                </div>

                <!-- Raindrop Sensor Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">☔</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-600">Rain Status</p>
                            <p class="text-lg font-bold {{ $raindrop > 0 ? 'text-indigo-600' : 'text-gray-600' }}">
                                {{ $raindrop > 0 ? 'Rain Detected' : 'No Rain' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div
                            class="w-3 h-3 rounded-full {{ $raindrop > 0 ? 'bg-indigo-500 animate-pulse' : 'bg-gray-300' }}">
                        </div>
                        <span class="text-sm text-gray-600">{{ $raindrop > 0 ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>

                <!-- Soil Moisture Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">🌱</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-600">Soil Moisture</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $soilMoisture }}%</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 h-2 rounded-full w-2/5"></div>
                    </div>
                </div>

                <!-- Flame Sensor Card -->
                <div
                    class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 {{ $flame > 0 ? 'bg-gradient-to-r from-red-500 to-orange-600' : 'bg-gradient-to-r from-green-500 to-emerald-600' }} rounded-xl flex items-center justify-center">
                            <span class="text-2xl">{{ $flame > 0 ? '🔥' : '✅' }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-600">Fire Status</p>
                            <p class="text-lg font-bold {{ $flame > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $flame > 0 ? 'Fire Detected!' : 'Safe' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full {{ $flame > 0 ? 'bg-red-500 animate-pulse' : 'bg-green-500' }}">
                        </div>
                        <span class="text-sm text-gray-600">{{ $flame > 0 ? 'Alert' : 'Normal' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button
                        class="flex items-center justify-center space-x-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add New Sensor</span>
                    </button>
                    <button
                        class="flex items-center justify-center space-x-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-medium hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span>Generate Report</span>
                    </button>
                    <button
                        class="flex items-center justify-center space-x-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-medium hover:from-amber-600 hover:to-orange-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Settings</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection