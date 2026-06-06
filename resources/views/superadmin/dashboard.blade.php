@extends('superadmin.layout.app')

@section('title', 'Dashboard Overview')

@section('content')
    <!-- Page Header -->
    <header class="bg-white rounded-xl shadow-sm p-4 md:p-5 mb-5 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Dashboard Overview</h1>
                <p class="text-gray-500 mt-1 text-sm md:text-base">Welcome back, {{ Auth::guard('superadmin')->user()->name ?? 'Super Admin' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('superadmin.profile') }}" class="flex items-center space-x-2 bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    @if(Auth::guard('superadmin')->user()->image)
                        <img src="{{ asset('storage/' . Auth::guard('superadmin')->user()->image) }}" alt="Profile" class="w-6 h-6 rounded-full object-cover">
                    @else
                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">{{ substr(Auth::guard('superadmin')->user()->name, 0, 1) }}</div>
                    @endif
                    <span>Profile</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Dashboard Content Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 mb-6">
        
        <!-- Widget 1 - Total Users -->
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-blue-500 p-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">45,231</p>
            <p class="text-xs text-gray-400 mt-2">↑ 12% from last month</p>
        </div>

        <!-- Widget 2 - Pending Orders -->
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-green-500 p-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Orders</p>
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">12</p>
            <p class="text-xs text-gray-400 mt-2">Awaiting processing</p>
        </div>
        
        <!-- Widget 3 - Galleries -->
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-yellow-500 p-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Galleries</p>
                <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center group-hover:bg-yellow-100 transition-colors">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">15</p>
            <p class="text-xs text-gray-400 mt-2">Total collections</p>
        </div>

        <!-- Widget 4 - System Alerts -->
        <div class="bg-white rounded-xl shadow-sm border-t-4 border-red-500 p-5 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">System Alerts</p>
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">2</p>
            <p class="text-xs text-gray-400 mt-2">Require attention</p>
        </div>
    </section>

    <!-- Recent Activity Section -->
    <section class="bg-white rounded-xl shadow-sm p-5 md:p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 tracking-tight">Recent System Activity</h2>
                <p class="text-sm text-gray-500 mt-1">Latest updates and system logs</p>
            </div>
            <button class="px-4 py-2 text-sm font-medium text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                View All
            </button>
        </div>
        
        <!-- Activity List -->
        <div class="space-y-3">
            <div class="flex items-start justify-between p-4 bg-gradient-to-r from-blue-50 to-transparent border-l-4 border-blue-500 rounded-lg hover:shadow-sm transition-shadow">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">User 'JaneDoe' updated profile settings.</p>
                        <p class="text-xs text-gray-500 mt-0.5">Profile information has been modified</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap ml-4">5 minutes ago</span>
            </div>
            
            <div class="flex items-start justify-between p-4 bg-gradient-to-r from-green-50 to-transparent border-l-4 border-green-500 rounded-lg hover:shadow-sm transition-shadow">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">New gallery created: "Summer Collection 2024".</p>
                        <p class="text-xs text-gray-500 mt-0.5">45 images uploaded successfully</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap ml-4">1 hour ago</span>
            </div>

            <div class="flex items-start justify-between p-4 bg-gradient-to-r from-yellow-50 to-transparent border-l-4 border-yellow-500 rounded-lg hover:shadow-sm transition-shadow">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">System maintenance scheduled</p>
                        <p class="text-xs text-gray-500 mt-0.5">Maintenance window: Tonight 2:00 AM - 4:00 AM</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap ml-4">3 hours ago</span>
            </div>
        </div>
    </section>
@endsection