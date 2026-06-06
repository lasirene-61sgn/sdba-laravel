<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin | @yield('title', 'Dashboard')</title>

    <!-- Tailwind CSS (v3.4+) via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                        sidebar: '#1d3557',
                        accent: '#4cc9f0',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Smooth transitions for sidebar */
        #sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Custom scrollbar for sidebar */
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="flex min-h-screen bg-gray-50 text-gray-700">

    <!-- Mobile Toggle Button -->
    <button id="mobileMenuButton"
        class="fixed top-4 left-4 z-20 p-2.5 rounded-lg bg-primary-600 text-white md:hidden shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-400 hover:bg-primary-700 transition-colors"
        aria-label="Toggle sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-10 w-64 md:w-56 lg:w-60 bg-gradient-to-b from-sidebar to-[#1a2a44] text-white shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static flex flex-col overflow-y-auto">
        <!-- Logo -->
        <div class="px-5 py-6 border-b border-gray-700/50">
            <div class="flex items-center space-x-3">
                @if(Auth::guard('superadmin')->user()->image)
                    <img src="{{ asset('storage/' . Auth::guard('superadmin')->user()->image) }}" alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-accent">
                @else
                    <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-white font-bold">{{ substr(Auth::guard('superadmin')->user()->name, 0, 1) }}</div>
                @endif
                <div>
                    <div class="text-xl font-bold text-accent tracking-tight">A-Panel</div>
                    <div class="text-xs text-gray-400 mt-1">Admin Dashboard</div>
                </div>
            </div>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="{{ route('superadmin.dashboard') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200
                    {{ Request::routeIs('superadmin.dashboard') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'text-gray-200 hover:bg-gray-700/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <div class="pt-6 pb-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 px-3">Management</p>
            </div>

            <a href="{{ route('superadmin.admins.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200
                    {{ Request::routeIs('superadmin.admins.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'text-gray-200 hover:bg-gray-700/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6m-6 0h-2M15 10a4 4 0 01-4 4H9a4 4 0 01-4-4V7a4 4 0 014-4h2a4 4 0 014 4v3zm0 0h6m-6 0h-2" />
                </svg>
                <span class="font-medium">Add/Manage Admins</span>
            </a>

            <a href="{{ route('admin.bills.index') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200
                    {{ Request::routeIs('admin.bills.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'text-gray-200 hover:bg-gray-700/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="font-medium">Bills</span>
            </a>

            <a href="{{ route('superadmin.profile') }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200
                    {{ Request::routeIs('superadmin.profile') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'text-gray-200 hover:bg-gray-700/50 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6m-6 0h-2M15 10a4 4 0 01-4 4H9a4 4 0 01-4-4V7a4 4 0 014-4h2a4 4 0 014 4v3zm0 0h6m-6 0h-2" />
                </svg>
                <span class="font-medium">Profile</span>
            </a>
        </nav>

        <!-- Logout Section -->
        <div class="p-4 border-t border-gray-700/50 mt-auto">
            <div class="flex items-center space-x-3 mb-3 px-1">
                @if(Auth::guard('superadmin')->user()->image)
                    <img src="{{ asset('storage/' . Auth::guard('superadmin')->user()->image) }}" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-500">
                @else
                    <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center text-white font-bold text-sm">{{ substr(Auth::guard('superadmin')->user()->name, 0, 1) }}</div>
                @endif
                <div class="text-xs text-gray-300">
                    <span class="text-gray-400">Logged in as:</span><br>
                    <span class="font-semibold text-white">{{ Auth::guard('superadmin')->user()->name ?? 'Super Admin' }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('superadmin.logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-all duration-200 hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay -->
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-black bg-opacity-40 z-5 hidden md:hidden transition-opacity duration-300"></div>

    <!-- Main Content -->
    <main class="flex-1 transition-all duration-300 p-4 md:p-5 lg:p-6 xl:p-8 bg-gray-50">

        @hasSection('pageHeader')
            <header class="bg-white rounded-xl shadow-sm p-4 md:p-5 mb-5 border border-gray-100">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">@yield('title', 'Dashboard')</h1>
                @hasSection('subtitle')
                    <p class="text-gray-500 mt-1 text-sm md:text-base">@yield('subtitle')</p>
                @endif
            </header>
        @endif

        @yield('content')

    </main>

    <!-- Scripts -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Toggle sidebar on mobile
        mobileMenuButton?.addEventListener('click', () => {
            const isOpen = sidebar.classList.toggle('translate-x-0');
            sidebarOverlay.classList.toggle('hidden');

            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('-translate-x-full');
            }
        });

        // Close sidebar when overlay is clicked
        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            sidebarOverlay.classList.add('hidden');
        });

        // Close sidebar on ESC key (mobile only)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                sidebarOverlay.classList.add('hidden');
            }
        });
    </script>

</body>

</html>