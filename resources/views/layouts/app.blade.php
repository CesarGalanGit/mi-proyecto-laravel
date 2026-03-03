<!DOCTYPE html>
<html lang="es" class="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Proyecto de estudio PHP/Laravel - CRUD de Usuarios">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PHP Prueba') — Laravel Studio</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
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
            class="flex flex-col h-full bg-gradient-to-b from-indigo-950 via-indigo-900 to-violet-950 text-white shadow-xl">

            {{-- Logo Area --}}
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-violet-400 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">Laravel Studio</h1>
                    <p class="text-xs text-indigo-300/70">PHP Learning Project</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <p class="px-3 mb-3 text-[10px] font-semibold uppercase tracking-widest text-indigo-400/60">Principal
                </p>

                <a href="/"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->is('/') ? 'bg-white/15 text-white shadow-sm' : 'text-indigo-200/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="/usuarios"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->is('usuarios*') ? 'bg-white/15 text-white shadow-sm' : 'text-indigo-200/80 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Usuarios
                    @if(isset($totalUsuarios))
                        <span
                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-400/20 text-indigo-300 border border-indigo-400/30">
                            {{ $totalUsuarios }}
                        </span>
                    @endif
                </a>
            </nav>

            {{-- Dark Mode Toggle --}}
            <div class="px-4 py-4 border-t border-white/10">
                <button id="darkModeToggle" onclick="toggleDarkMode()"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-indigo-200/80 hover:bg-white/10 hover:text-white transition-all duration-200">
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
                PHP Prueba &copy; {{ date('Y') }} — Proyecto de estudio con Laravel
                {{ Illuminate\Foundation\Application::VERSION }}
            </p>
        </footer>
    </div>

    {{-- ============================================
    TOAST CONTAINER
    ============================================ --}}
    <div id="toastContainer" class="toast-container"></div>

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
            @if(session('success'))
                showToast(@json(session('success')), 'success');
            @endif

            @if(session('error'))
                showToast(@json(session('error')), 'error');
            @endif

            @if(session('info'))
                showToast(@json(session('info')), 'info');
            @endif
        });
    </script>

    @yield('scripts')
</body>

</html>