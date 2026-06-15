<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            overflow-x: hidden;
        }

        /* ============================================
           SIDEBAR STYLES
        ============================================ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 1.5rem 0;
            z-index: 1000;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Logo Section */
        .logo {
            padding: 1rem 1.5rem 1.5rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        /* Navigation */
        .sidebar nav {
            flex: 1;
            padding: 0 1rem;
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            margin-bottom: 0.375rem;
            border-radius: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(4px);
        }

        .sidebar nav a.bg-indigo-700 {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.15);
        }

        .sidebar nav a svg {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.875rem;
            flex-shrink: 0;
        }

        /* Logout Section */
        .logout-section {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin-top: auto;
        }

        .user-info {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.75rem;
            padding: 0 0.25rem;
        }

        .logout-section form button {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            background-color: #dc2626;
            color: white;
            font-weight: 600;
            font-size: 0.9375rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .logout-section form button:hover {
            background-color: #b91c1c;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .logout-section form button svg {
            width: 1.125rem;
            height: 1.125rem;
            margin-right: 0.5rem;
        }

        /* ============================================
           MOBILE MENU BUTTON
        ============================================ */
        .mobile-menu-button {
            position: right;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            padding: 0.75rem 1rem;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: all 0.2s ease;
        }

        .mobile-menu-button:hover {
            background-color: #4338ca;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }

        .mobile-menu-button svg {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
        }

       
        /* Page Header */
        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
            letter-spacing: -0.5px;
        }

        .page-header p {
            font-size: 0.9375rem;
            color: #6b7280;
            margin: 0;
        }

        /* Helpline Section */
        .helpline-section {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid #e5e7eb;
        }

        .helpline-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .helpline-content {
            font-size: 0.9375rem;
            color: #374151;
            line-height: 1.6;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            border-left: 3px solid #4f46e5;
            border-radius: 0.5rem;
        }

        /* ============================================
           RESPONSIVE BREAKPOINTS
        ============================================ */

        /* Tablet Portrait and Up (768px+) */
        @media (min-width: 768px) {
            .sidebar {
                width: 260px;
            }

            .main-content {
                padding: 1.5rem;
            }

            .page-header {
                padding: 1.75rem 2rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }

        /* Tablet Landscape and Small Desktop (1024px+) */
        @media (min-width: 1024px) {
            .mobile-menu-button {
                display: none;
            }

            .sidebar {
                transform: translateX(0);
                width: 260px;
            }

            .main-content {
                margin-left: 260px;
                padding: 1.5rem 2rem;
            }

            .content-wrapper {
                max-width: 100%;
            }
        }

        /* Desktop (1280px+) */
        @media (min-width: 1280px) {
            .sidebar {
                width: 280px;
            }

            .main-content {
                margin-left: 280px;
                padding: 2rem 2.5rem;
            }

            .page-header {
                padding: 2rem 2.5rem;
            }

            .page-header h1 {
                font-size: 2.25rem;
            }
        }

        /* Large Desktop (1536px+) */
        @media (min-width: 1536px) {
            .sidebar {
                width: 300px;
            }

            .main-content {
                margin-left: 300px;
                padding: 2.5rem 3rem;
            }

            .content-wrapper {
                max-width: 1400px;
            }
        }

        /* Mobile Landscape */
        @media (max-width: 1023px) and (orientation: landscape) {
            .sidebar {
                width: 260px;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Small Mobile (375px and below) */
        @media (max-width: 375px) {
            .sidebar {
                width: 260px;
            }

            .mobile-menu-button {
                padding: 0.625rem 0.875rem;
                font-size: 0.875rem;
            }

            .page-header {
                padding: 1.25rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .sidebar nav a {
                padding: 0.75rem 0.875rem;
                font-size: 0.875rem;
            }
        }

        /* ============================================
           SIDEBAR OVERLAY FOR MOBILE
        ============================================ */
        @media (max-width: 1023px) {
            .sidebar.active::before {
                content: '';
                position: fixed;
                top: 0;
                left: 280px;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: -1;
                animation: fadeIn 0.3s ease;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* ============================================
           UTILITY CLASSES
        ============================================ */
        .space-y-1 > * + * {
            margin-top: 0.25rem;
        }

        /* Print Styles */
        @media print {
            .sidebar,
            .mobile-menu-button {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Focus Styles for Accessibility */
        .sidebar nav a:focus,
        .mobile-menu-button:focus,
        .logout-section form button:focus {
            outline: 2px solid white;
            outline-offset: 2px;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Selection Color */
        ::selection {
            background-color: #4f46e5;
            color: white;
        }
    </style>
    
</head>

<body>
    <!-- Mobile menu button -->
    <div class="flex justify-end">
    <button class="mobile-menu-button" id="mobileMenuButton">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>


    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div>
            <div class="logo flex items-center space-x-3">
                @if(session()->has('committee_member'))
                    <!-- Committee Member Profile -->
                    @php $committee = session('committee_member'); @endphp
                    @if($committee->image_path)
                        <img src="{{ asset('storage/' . $committee->image_path) }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white">
                    @else
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold">{{ substr($committee->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <div>{{ $committee->post_name }} Panel</div>
                        <div class="text-xs font-normal mt-1">{{ $committee->name }}</div>
                    </div>
                @else
                    <!-- Admin Profile -->
                    @if(Auth::guard('admin')->user()->image)
                        <img src="{{ asset('storage/' . Auth::guard('admin')->user()->image) }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white">
                    @else
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold">{{ substr(Auth::guard('admin')->user()->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <div>Admin Panel</div>
                        <div class="text-xs font-normal mt-1">{{ Auth::guard('admin')->user()->company_name ?? 'Company' }}</div>
                    </div>
                @endif
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1">
                @php
                    $adminPermissions = Auth::guard('admin')->check() ? (Auth::guard('admin')->user()->sidebar_permissions ?? []) : [];
                    $allPermissions = [
                        'dashboard' => [
                            'route' => 'admin.dashboard',
                            'label' => 'Dashboard',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'
                        ],
                        'gallery' => [
                            'route' => 'admin.gallery.index',
                            'label' => 'Gallery',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'
                        ],
                        'banner' => [
                            'route' => 'admin.banner.index',
                            'label' => 'Banner',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>'
                        ],
                        'notice' => [
                            'route' => 'admin.notice.index',
                            'label' => 'Notice',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
                        ],
                        'village' => [
                            'route' => 'admin.village.index',
                            'label' => 'Village',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'
                        ],
                        'event' => [
                            'route' => 'admin.event.index',
                            'label' => 'Event',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'
                        ],
                        'news' => [
                            'route' => 'admin.news.index',
                            'label' => 'News',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>'
                        ],
                        'support' => [
                            'route' => 'admin.supports.index',
                            'label' => 'Support',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        ],
                        'committee' => [
                            'route' => 'admin.committee.index',
                            'label' => 'Committee',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>'
                        ],
                        'customer' => [
                            'route' => 'admin.customer.index',
                            'label' => 'Members',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>'
                        ],
                        'customer_plan' => [
                            'route' => 'admin.customer-plan.index',
                            'label' => 'Customers Plans',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>'
                        ],
                        'bills' => [
                            'route' => 'admin.bills.index',
                            'label' => 'Bills',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
                        ],
                        'about_us' => [
                            'route' => 'admin.about-us.index',
                            'label' => 'About Us',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        ],
                        'polls' => [
                            'route' => 'admin.polls.index',
                            'label' => 'Polls',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>'
                        ],
                        'helpline' => [
                            'route' => 'admin.helpline.index',
                            'label' => 'Helpline',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        ],
                        'links' => [
                            'route' => 'admin.links.index',
                            'label' => 'Links',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        ],
                    ];
                @endphp

                @foreach($allPermissions as $key => $item)
                    @if(empty($adminPermissions) || in_array($key, $adminPermissions))
                        <a href="{{ route($item['route']) }}"
                            class="@if(Request::routeIs(str_replace('.', '*', $item['route']))) bg-indigo-700 @endif">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                {!! $item['icon'] !!}
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <!-- Logout Section -->
        <div class="logout-section">
            <div class="flex items-center space-x-3 mb-3">
                @if(session()->has('committee_member'))
                    <!-- Committee Member Profile -->
                    @php $committee = session('committee_member'); @endphp
                    @if($committee->image_path)
                        <img src="{{ asset('storage/' . $committee->image_path) }}" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-300">
                    @else
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold text-sm">{{ substr($committee->name, 0, 1) }}</div>
                    @endif
                    <div class="user-info">
                        <span class="text-gray-300">Logged in as:</span><br>
                        <span class="font-semibold text-white">{{ $committee->name ?? 'Committee Member' }}</span><br>
                        <span class="text-xs text-gray-300">{{ $committee->post_name }}</span>
                    </div>
                @else
                    <!-- Admin Profile -->
                    @if(Auth::guard('admin')->user()->image)
                        <img src="{{ asset('storage/' . Auth::guard('admin')->user()->image) }}" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-300">
                    @else
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold text-sm">{{ substr(Auth::guard('admin')->user()->name, 0, 1) }}</div>
                    @endif
                    <div class="user-info">
                        <span class="text-gray-300">Logged in as:</span><br>
                        <span class="font-semibold text-white">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                    </div>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Display flash messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Dynamic Content Section -->
            @yield('content')
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.getElementById('sidebar');

        mobileMenuButton.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(event.target) && event.target !== mobileMenuButton && !mobileMenuButton.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Close sidebar on route change (mobile)
        if (window.innerWidth <= 1024) {
            const navLinks = sidebar.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                });
            });
        }

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('active');
                }
            }, 250);
        });
    </script>
    @yield('scripts')
</body>

</html>