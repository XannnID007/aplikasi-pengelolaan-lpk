<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - LPK Jepang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">
    <!-- Sidebar -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-center h-16 bg-primary-600 text-white">
            <div class="flex items-center space-x-2">
                <i class="fas fa-torii-gate text-xl"></i>
                <span class="text-lg font-semibold">LPK Jepang</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-8">
            <div class="px-4 space-y-2">
                <a href="{{ route('peserta.dashboard') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 {{ request()->routeIs('peserta.dashboard') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('peserta.profil') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 {{ request()->routeIs('peserta.profil*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : '' }}">
                    <i class="fas fa-user-edit w-5 h-5 mr-3"></i>
                    <span>Lengkapi Profil</span>
                </a>

                <a href="{{ route('peserta.dokumen') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 {{ request()->routeIs('peserta.dokumen*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : '' }}">
                    <i class="fas fa-file-upload w-5 h-5 mr-3"></i>
                    <span>Upload Dokumen</span>
                </a>

                <a href="{{ route('peserta.jadwal') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 {{ request()->routeIs('peserta.jadwal*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : '' }}">
                    <i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
                    <span>Jadwal Keberangkatan</span>
                </a>

                <a href="{{ route('peserta.status') }}"
                    class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-colors duration-200 {{ request()->routeIs('peserta.status*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : '' }}">
                    <i class="fas fa-list-ul w-5 h-5 mr-3"></i>
                    <span>Status Pendaftaran</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Navbar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <!-- Mobile menu button -->
                <button id="mobile-menu-btn"
                    class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Page title -->
                <div class="flex-1 lg:flex-none">
                    <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                </div>

                <!-- User menu -->
                <div class="relative">
                    <button id="user-menu-btn"
                        class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <div
                            class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center text-white font-medium">
                            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                        </div>
                        <span class="hidden lg:block text-gray-700">{{ auth()->user()->nama }}</span>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </button>

                    <!-- Dropdown menu -->
                    <div id="user-menu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="{{ route('peserta.profil') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-edit w-4 h-4 mr-3"></i>
                            Profil
                        </a>
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            <!-- Alerts -->
            @if (session('success'))
                <div
                    class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Sidebar overlay for mobile -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        mobileMenuBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        // User menu functionality
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenu = document.getElementById('user-menu');

        userMenuBtn?.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });

        // Close user menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenuBtn?.contains(e.target) && !userMenu?.contains(e.target)) {
                userMenu?.classList.add('hidden');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>
