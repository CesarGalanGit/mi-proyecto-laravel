<!DOCTYPE html>
<html lang="es" class="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Proyecto de estudio PHP/Laravel - CRUD de Usuarios">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Laravel Studio') — Laravel Studio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen flex antialiased">

    {{-- ============================================
    SIDEBAR
    ============================================ --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-64 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div
            class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-sky-950 to-cyan-950 text-white shadow-xl">

            {{-- Logo Area --}}
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-400 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">Laravel Studio</h1>
                    <p class="text-xs text-sky-200/70">Panel profesional</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <p class="px-3 mb-3 text-[10px] font-semibold uppercase tracking-widest text-sky-300/60">Principal
                </p>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->is('/') ? 'bg-white/15 text-white shadow-sm' : 'text-sky-100/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('usuarios.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->is('usuarios*') ? 'bg-white/15 text-white shadow-sm' : 'text-sky-100/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Usuarios
                    @if(isset($totalUsuarios))
                        <span
                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-400/20 text-sky-200 border border-sky-400/30">
                            {{ $totalUsuarios }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('shop.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->is('tienda*') ? 'bg-white/15 text-white shadow-sm' : 'text-sky-100/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17h6m-6 0a2 2 0 11-4 0m4 0a2 2 0 104 0m6 0a2 2 0 11-4 0m4 0H9m0 0V5a1 1 0 011-1h6a1 1 0 011 1v12m-8 0h8" />
                    </svg>
                    Tienda
                </a>

                @can('manage-users')
                    <p class="px-3 mt-5 mb-3 text-[10px] font-semibold uppercase tracking-widest text-sky-300/60">Admin tienda</p>

                    <a href="{{ route('admin.mcp-token.show') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->is('admin/mcp-token') ? 'bg-white/15 text-white shadow-sm' : 'text-sky-100/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 11c0 1.657-1.343 3-3 3S6 12.657 6 11s1.343-3 3-3 3 1.343 3 3zm0 0h6m-3-3v6" />
                        </svg>
                        Token MCP
                    </a>

                    <a href="{{ route('admin.shop.cars.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->is('admin/tienda/coches*') ? 'bg-white/15 text-white shadow-sm' : 'text-sky-100/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17h6m-6 0a2 2 0 11-4 0m4 0a2 2 0 104 0m6 0a2 2 0 11-4 0m4 0H9m0 0V5a1 1 0 011-1h6a1 1 0 011 1v12m-8 0h8" />
                        </svg>
                        Gestión coches
                    </a>

                    <a href="{{ route('admin.shop.orders.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                              {{ request()->is('admin/tienda/pedidos*') ? 'bg-white/15 text-white shadow-sm' : 'text-sky-100/80 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Gestión pedidos
                    </a>
                @endcan
            </nav>

            {{-- Dark Mode Toggle --}}
            <div class="px-4 py-4 border-t border-white/10">
                <button id="darkModeToggle" onclick="toggleDarkMode()"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-sky-100/80 hover:bg-white/10 hover:text-white transition-all duration-200">
                    {{-- Sun icon (visible in dark mode) --}}
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{-- Moon icon (visible in light mode) --}}
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="dark:hidden">Modo Oscuro</span>
                    <span class="hidden dark:inline">Modo Claro</span>
                </button>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

    {{-- ============================================
    MAIN CONTENT
    ============================================ --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        {{-- Top Bar --}}
        <header
            class="sticky top-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-b border-slate-200/60 dark:border-slate-800/60">
            <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                {{-- Mobile menu button --}}
                <button onclick="toggleSidebar()"
                    class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Page Title --}}
                <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">
                    @yield('page-title', 'Dashboard')
                </h2>

                {{-- Right side --}}
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 dark:text-slate-500 hidden sm:block">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }}
                    </span>

                    <a href="{{ route('shop.index') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white/70 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6m-6 0a2 2 0 11-4 0m4 0a2 2 0 104 0m6 0a2 2 0 11-4 0m4 0H9m0 0V5a1 1 0 011-1h6a1 1 0 011 1v12m-8 0h8" />
                        </svg>
                        Tienda
                    </a>

                    @auth
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100/70 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-white text-[11px] font-bold">
                                {{ mb_substr(auth()->user()->email, 0, 1) }}
                            </span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 max-w-[220px] truncate">
                                {{ auth()->user()->email }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white/70 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition-all shadow-md shadow-cyan-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17l5-5m0 0l-5-5m5 5H3" />
                            </svg>
                            Iniciar sesión
                        </a>
                    @endguest
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="px-4 sm:px-6 lg:px-8 py-4 border-t border-slate-200/60 dark:border-slate-800/60">
            <p class="text-xs text-center text-slate-400 dark:text-slate-600">
                Laravel Studio &copy; {{ date('Y') }} — Gestión de usuarios con Laravel
                {{ Illuminate\Foundation\Application::VERSION }}
            </p>
        </footer>
    </div>

    {{-- ============================================
    TOAST CONTAINER
    ============================================ --}}
    <div id="toastContainer" class="toast-container"></div>

    <div
        id="flashMessages"
        class="hidden"
        data-success='@json(session('success'))'
        data-error='@json(session('error'))'
        data-info='@json(session('info'))'
    ></div>

    {{-- ============================================
    GLOBAL SCRIPTS
    ============================================ --}}
    <script>
        // ---- Dark Mode ----
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Apply saved dark mode preference on load
        (function () {
            const savedDarkMode = localStorage.getItem('darkMode');
            if (savedDarkMode === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();

        // ---- Mobile Sidebar ----
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // ---- Toast Notifications ----
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');

            const iconMap = {
                success: `<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                error: `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                info: `<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            };

            const bgMap = {
                success: 'bg-emerald-50 dark:bg-emerald-950/50 border-emerald-200 dark:border-emerald-800',
                error: 'bg-red-50 dark:bg-red-950/50 border-red-200 dark:border-red-800',
                info: 'bg-blue-50 dark:bg-blue-950/50 border-blue-200 dark:border-blue-800'
            };

            toast.className = `toast flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg ${bgMap[type] || bgMap.info}`;
            toast.innerHTML = `
                ${iconMap[type] || iconMap.info}
                <p class="text-sm font-medium text-slate-700 dark:text-slate-200 flex-1">${message}</p>
                <button onclick="this.parentElement.classList.add('toast-exit'); setTimeout(() => this.parentElement.remove(), 300);"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;

            container.appendChild(toast);

            // Auto-dismiss after 4 seconds
            setTimeout(() => {
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Show flash messages as toasts
        document.addEventListener('DOMContentLoaded', function () {
            const flashEl = document.getElementById('flashMessages');
            const flashes = flashEl ? {
                success: JSON.parse(flashEl.dataset.success || 'null'),
                error: JSON.parse(flashEl.dataset.error || 'null'),
                info: JSON.parse(flashEl.dataset.info || 'null'),
            } : { success: null, error: null, info: null };

            if (flashes.success) {
                showToast(flashes.success, 'success');
            }

            if (flashes.error) {
                showToast(flashes.error, 'error');
            }

            if (flashes.info) {
                showToast(flashes.info, 'info');
            }
        });
    </script>

    @yield('scripts')
</body>

</html>
