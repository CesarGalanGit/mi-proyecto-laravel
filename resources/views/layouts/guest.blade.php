<!DOCTYPE html>
<html lang="es" class="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Proyecto de estudio PHP/Laravel - CRUD de Usuarios">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Acceso') — Laravel Studio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen antialiased text-slate-900 dark:text-slate-100">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(1200px_circle_at_15%_5%,rgba(14,165,233,0.20),transparent_58%),radial-gradient(900px_circle_at_90%_25%,rgba(16,185,129,0.16),transparent_58%),linear-gradient(to_bottom,rgba(248,250,252,1),rgba(240,249,255,1))] dark:bg-[radial-gradient(1200px_circle_at_15%_5%,rgba(14,165,233,0.18),transparent_58%),radial-gradient(900px_circle_at_90%_25%,rgba(16,185,129,0.14),transparent_58%),linear-gradient(to_bottom,rgba(2,6,23,1),rgba(3,15,31,1))]"></div>

        <div class="relative z-10">
            <header class="px-4 sm:px-6 lg:px-8 pt-6">
                <div class="max-w-6xl mx-auto flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-cyan-500/20 group-hover:shadow-cyan-500/30 transition">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Laravel Studio</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Panel de gestión</p>
                        </div>
                    </a>

                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition">
                        Volver al inicio
                    </a>
                </div>
            </header>

            <main class="px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
                <div class="max-w-6xl mx-auto min-h-[calc(100vh-11rem)] flex items-center">
                    @yield('content')
                </div>
            </main>

            <footer class="px-4 sm:px-6 lg:px-8 pb-8">
                <p class="text-xs text-center text-slate-500 dark:text-slate-500">
                    Laravel Studio &copy; {{ date('Y') }} — Laravel v{{ Illuminate\Foundation\Application::VERSION }}
                </p>
            </footer>
        </div>
    </div>

    @yield('scripts')
</body>

</html>
