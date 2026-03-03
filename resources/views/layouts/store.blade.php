<!DOCTYPE html>
<html lang="es" class="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Escaparate de coches con anuncios oficiales en Laravel Studio">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tienda') — Laravel Studio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <div class="relative min-h-screen overflow-x-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(1000px_circle_at_10%_5%,rgba(14,165,233,0.18),transparent_58%),radial-gradient(900px_circle_at_88%_15%,rgba(16,185,129,0.15),transparent_58%),linear-gradient(to_bottom,rgba(248,250,252,1),rgba(240,249,255,1))] dark:bg-[radial-gradient(1000px_circle_at_10%_5%,rgba(14,165,233,0.15),transparent_58%),radial-gradient(900px_circle_at_88%_15%,rgba(16,185,129,0.12),transparent_58%),linear-gradient(to_bottom,rgba(2,6,23,1),rgba(3,15,31,1))]"></div>

        <header class="sticky top-0 z-20 backdrop-blur border-b border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-950/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 group">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6m-6 0a2 2 0 11-4 0m4 0a2 2 0 104 0m6 0a2 2 0 11-4 0m4 0H9m0 0V5a1 1 0 011-1h6a1 1 0 011 1v12m-8 0h8" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Laravel Studio Motors</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Anuncios oficiales de portales</p>
                    </div>
                </a>

                <nav class="hidden lg:flex items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('shop.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('shop.index') || request()->routeIs('shop.show') ? 'bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }} transition">Catálogo</a>
                </nav>

                <div class="flex items-center gap-2 sm:gap-3">
                    <button type="button" onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Cambiar modo de color">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <a href="{{ route('usuarios.index') }}" class="hidden sm:inline-flex px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">Gestión usuarios</a>

                    @auth
                        @can('manage-users')
                            <a href="{{ route('admin.shop.cars.index') }}" class="hidden sm:inline-flex px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition">Admin tienda</a>
                        @endcan

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition shadow-md shadow-cyan-500/20">
                                Cerrar sesión
                            </button>
                        </form>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition shadow-md shadow-cyan-500/20">Iniciar sesión</a>
                    @endguest
                </div>
            </div>

            <div class="lg:hidden border-t border-slate-200/70 dark:border-slate-800/70 px-4 py-2.5 flex items-center justify-between gap-2 text-xs font-semibold">
                <a href="{{ route('shop.index') }}" class="px-2.5 py-1.5 rounded-lg {{ request()->routeIs('shop.index') || request()->routeIs('shop.show') ? 'bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300' : 'text-slate-600 dark:text-slate-300' }}">Catálogo</a>
                @auth
                    @can('manage-users')
                        <a href="{{ route('admin.shop.cars.index') }}" class="px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-300">Admin</a>
                    @endcan
                @endauth
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/40 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            const savedDarkMode = localStorage.getItem('darkMode');
            if (savedDarkMode === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</body>

</html>
