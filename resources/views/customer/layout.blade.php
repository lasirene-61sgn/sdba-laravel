<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portal | @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Modern, professional color palette for customer portal */
            --primary: #4361ee;
            /* Modern blue */
            --primary-dark: #3a56d4;
            /* Darker blue for hover states */
            --primary-light: #edf2ff;
            /* Light blue for backgrounds */
            --secondary: #7209b7;
            /* Purple accent */
            --accent: #4cc9f0;
            /* Teal accent */
            --dark: #1d3557;
            /* Deep blue-gray */
            --light: #f8f9fa;
            /* Light background */
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
            --success: #4ade80;
            /* Modern green */
            --warning: #facc15;
            /* Modern yellow */
            --danger: #f87171;
            /* Modern red */
            --sidebar-bg: #1d3557;
            /* Deep blue for sidebar */
            --sidebar-text: #f1f5f9;
            /* Light text */
            --header-bg: #ffffff;
            --content-bg: #f5f7fb;
            /* Softer background */
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: var(--content-bg);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #1a2a44 100%);
            color: var(--sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar .logo {
            font-weight: 800;
            font-size: 1.75rem;
            color: var(--accent);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.75rem 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            margin: 0.375rem 0.75rem;
            border-radius: 0.625rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.25s ease;
            font-weight: 500;
        }

        .sidebar nav a:hover {
            background-color: rgba(67, 97, 238, 0.2);
            color: white;
            transform: translateX(3px);
        }

        .sidebar nav a.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        }

        .sidebar nav a svg {
            margin-right: 0.875rem;
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
        }

        .sidebar .logout-section {
            margin-top: auto;
            padding: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .user-info {
            font-size: 0.9375rem;
            color: var(--gray-400);
            margin-bottom: 1.25rem;
            padding: 0 0.5rem;
        }

        .main-content {
            margin-left: 260px;
            transition: margin 0.3s ease;
            min-height: 100vh;
            background-color: var(--content-bg);
        }

        .content-wrapper {
            padding: 2.25rem;
        }

        .page-header {
            background-color: var(--header-bg);
            padding: 1.75rem 2.25rem;
            margin: -2.25rem -2.25rem 2.25rem -2.25rem;
            border-bottom: 1px solid var(--border-color);
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .page-header p {
            color: var(--gray-600);
            margin: 0.375rem 0 0 0;
            font-size: 1.05rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.25s ease;
            border: none;
            cursor: pointer;
            padding: 0.625rem 1.125rem;
            font-size: 0.9375rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.3);
        }

        .btn-primary {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(120deg, var(--primary-dark) 0%, #2e4bd9 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
        }

        .btn-secondary {
            background-color: var(--gray-600);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--gray-700);
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(120deg, var(--success) 0%, #22c55e 100%);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(120deg, #22c55e 0%, #16a34a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(34, 197, 94, 0.3);
        }

        .btn-danger {
            background: linear-gradient(120deg, var(--danger) 0%, #ef4444 100%);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(120deg, #ef4444 0%, #dc2626 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
        }

        .btn-warning {
            background: linear-gradient(120deg, var(--warning) 0%, #eab308 100%);
            color: white;
        }

        .btn-warning:hover {
            background: linear-gradient(120deg, #eab308 0%, #ca8a04 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(234, 179, 8, 0.3);
        }

        .btn-info {
            background: linear-gradient(120deg, var(--accent) 0%, #06b6d4 100%);
            color: white;
        }

        .btn-info:hover {
            background: linear-gradient(120deg, #06b6d4 0%, #0891b2 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(6, 182, 212, 0.3);
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 1.75rem;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            padding: 1.375rem 1.625rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--dark);
            font-size: 1.25rem;
        }

        .card-body {
            padding: 1.625rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
            color: var(--gray-700);
            background-color: #ffffff;
            background-clip: padding-box;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus {
            color: var(--gray-700);
            background-color: #ffffff;
            border-color: var(--primary);
            outline: 0;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .form-label {
            display: block;
            margin-bottom: 0.625rem;
            font-weight: 500;
            color: var(--gray-700);
            font-size: 1rem;
        }

        .alert {
            padding: 1.125rem;
            border-radius: 0.625rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background: linear-gradient(120deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-danger {
            background: linear-gradient(120deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-warning {
            background: linear-gradient(120deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .alert-info {
            background: linear-gradient(120deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0c4a6e;
            border: 1px solid #7dd3fc;
        }

        .mobile-menu-button {
            display: none;
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.875rem 1.25rem;
            border-radius: 0.625rem;
            cursor: pointer;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
            font-weight: 500;
            transition: all 0.25s ease;
        }

        .mobile-menu-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(67, 97, 238, 0.4);
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-button {
                display: block;
            }

            .content-wrapper {
                padding: 1.75rem;
            }

            .page-header {
                padding: 1.5rem;
                margin: -1.75rem -1.75rem 1.75rem -1.75rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
            }

            .card-header {
                padding: 1.125rem 1.375rem;
                font-size: 1.125rem;
            }

            .card-body {
                padding: 1.375rem;
            }
        }

        @media (max-width: 480px) {
            .content-wrapper {
                padding: 1.25rem;
            }

            .page-header {
                padding: 1.125rem;
                margin: -1.25rem -1.25rem 1.25rem -1.25rem;
            }

            .page-header h1 {
                font-size: 1.625rem;
            }

            .card-body {
                padding: 1.125rem;
            }

            .btn {
                padding: 0.5rem 0.875rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile menu button -->
    <button class="mobile-menu-button" id="mobileMenuButton">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        Menu
    </button>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div>
            <div class="logo">
                Customer Portal
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1">
                @php
                    // Get the customer's admin and their sidebar permissions
                    $customer = Auth::guard('customer')->user();
                    $admin = $customer ? $customer->admin : null;
                    $adminPermissions = $admin ? $admin->sidebar_permissions : [];
                    
                    // Define all possible sidebar items with their routes and labels
                    // Only show items that are in the admin's permissions
                    $allPermissions = [
                        'dashboard' => [
                            'route' => 'customer.dashboard',
                            'label' => 'Dashboard',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'
                        ]
                    ];
                    
                    // Conditionally add items based on admin permissions
                    if ($admin) {
                        // Always show banner if admin has permission for it
                        if (empty($adminPermissions) || in_array('banner', $adminPermissions)) {
                            $allPermissions['banner'] = [
                                'route' => 'customer.banner',
                                'label' => 'Banner',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'
                            ];
                        }
                        
                        // Always show notice if admin has permission for it
                        if (empty($adminPermissions) || in_array('notice', $adminPermissions)) {
                            $allPermissions['notice'] = [
                                'route' => 'customer.notice',
                                'label' => 'Notice',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
                            ];
                        }
                        
                        // Always show gallery if admin has permission for it
                        if (empty($adminPermissions) || in_array('gallery', $adminPermissions)) {
                            $allPermissions['gallery'] = [
                                'route' => 'customer.gallery',
                                'label' => 'Gallery',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'
                            ];
                        }
                        
                        // Always show event if admin has permission for it
                        if (empty($adminPermissions) || in_array('event', $adminPermissions)) {
                            $allPermissions['event'] = [
                                'route' => 'customer.event',
                                'label' => 'Event',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'
                            ];
                        }
                        
                        // Always show news if admin has permission for it
                        if (empty($adminPermissions) || in_array('news', $adminPermissions)) {
                            $allPermissions['news'] = [
                                'route' => 'customer.news',
                                'label' => 'News',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>'
                            ];
                        }
                        
                        // Always show support if admin has permission for it
                        if (empty($adminPermissions) || in_array('support', $adminPermissions)) {
                            $allPermissions['support'] = [
                                'route' => 'customer.support',
                                'label' => 'Support',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M19 12a7 7 0 11-14 0 7 7 0 0114 0zm0 0l-5 5m5-5l-5-5"></path>'
                            ];
                        }
                        
                        // Always show committee if admin has permission for it
                        if (empty($adminPermissions) || in_array('committee', $adminPermissions)) {
                            $allPermissions['committee'] = [
                                'route' => 'customer.committee',
                                'label' => 'Committee',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>'
                            ];
                        }
                        
                        // Always show customer list for customers (they should always be able to see other customers from their admin)
                        $allPermissions['customer'] = [
                            'route' => 'customer.list',
                            'label' => 'All Customers',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>'
                        ];
                        
                        // Always show customer plan if admin has permission for it
                        if (empty($adminPermissions) || in_array('customer_plan', $adminPermissions)) {
                            $allPermissions['customer_plan'] = [
                                'route' => 'customer.customer_plan',
                                'label' => 'Customer Plans',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>'
                            ];
                        }
                        
                        // Always show about us if admin has permission for it
                        if (empty($adminPermissions) || in_array('about_us', $adminPermissions)) {
                            $allPermissions['about_us'] = [
                                'route' => 'customer.about-us',
                                'label' => 'About Us',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                            ];
                        }
                    }
                @endphp

                @foreach($allPermissions as $key => $item)
                    @if(empty($adminPermissions) || in_array($key, $adminPermissions) || $key === 'customer')
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center p-3 rounded-lg text-white hover:bg-indigo-600 transition duration-150 @if(Request::routeIs(str_replace('.', '*', $item['route']))) bg-indigo-700 @endif">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
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
            <div class="user-info">Logged in as: {{ Auth::guard('customer')->user()->name ?? 'Customer' }}</div>
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit"
                    class="w-full p-3 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition duration-150 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
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
            <!-- Header -->
            <header class="page-header">
                <h1>@yield('title')</h1>
                <p>@yield('subtitle')</p>
            </header>

            <!-- Dynamic Content Section -->
            @yield('content')
        </div>
    </main>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuButton').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuButton = document.getElementById('mobileMenuButton');

            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(event.target) && event.target !== mobileMenuButton) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>