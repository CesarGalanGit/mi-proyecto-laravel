@extends('layouts.guest')

@section('title', 'Registro')

@section('content')
<div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:gap-10 items-start lg:items-center">
    <section class="animate-fade-in space-y-6 lg:pr-6">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/65 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/70 text-slate-700 dark:text-slate-200 backdrop-blur">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Crear cuenta
        </span>

        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Bienvenido a Laravel Studio
            </h1>
            <p class="mt-3 text-slate-600 dark:text-slate-300 leading-relaxed max-w-xl">
                Regístrate para obtener acceso al panel de control, gestionar usuarios y más.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/50 px-4 py-3 backdrop-blur">
                <p class="font-semibold text-slate-900 dark:text-white">Fácil y rápido</p>
                <p class="mt-1 text-slate-500 dark:text-slate-400">Sólo toma unos segundos.</p>
            </div>
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/50 px-4 py-3 backdrop-blur">
                <p class="font-semibold text-slate-900 dark:text-white">Seguridad</p>
                <p class="mt-1 text-slate-500 dark:text-slate-400">Tus datos están protegidos.</p>
            </div>
        </div>
    </section>

    <section class="animate-slide-up delay-1 bg-white/85 dark:bg-slate-900/80 rounded-3xl border border-slate-200/70 dark:border-slate-800/70 shadow-xl shadow-slate-950/5 overflow-hidden backdrop-blur">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="text-[10px] uppercase tracking-wider font-bold text-slate-400 dark:text-slate-500 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                            Inicio
                        </a>
                        <span class="text-slate-300 dark:text-slate-700">|</span>
                        <p class="text-[11px] uppercase tracking-widest font-semibold text-slate-500 dark:text-slate-400">Registro</p>
                    </div>
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">Laravel Studio</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-linear-to-br from-purple-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="p-6 sm:p-7 space-y-5">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-4" role="alert" aria-live="polite">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-red-700 dark:text-red-400 text-sm">No se pudo registrar</p>
                            <ul class="mt-1 text-sm text-red-600 dark:text-red-400/80 list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nombre completo</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    autofocus
                    required
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-red-300 dark:border-red-700 focus:ring-red-400/20 focus:border-red-400' : 'border-slate-200 dark:border-slate-700 focus:ring-cyan-400/25 focus:border-cyan-500' }} rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none transition"
                    placeholder="Juan Pérez"
                >
            </div>

            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Correo electrónico</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    inputmode="email"
                    required
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('email') ? 'border-red-300 dark:border-red-700 focus:ring-red-400/20 focus:border-red-400' : 'border-slate-200 dark:border-slate-700 focus:ring-cyan-400/25 focus:border-cyan-500' }} rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none transition"
                    placeholder="ejemplo@correo.com"
                >
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Contraseña</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('password') ? 'border-red-300 dark:border-red-700 focus:ring-red-400/20 focus:border-red-400' : 'border-slate-200 dark:border-slate-700 focus:ring-cyan-400/25 focus:border-cyan-500' }} rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none transition"
                    placeholder="Mínimo 8 caracteres"
                >
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirmar contraseña</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('password_confirmation') ? 'border-red-300 dark:border-red-700 focus:ring-red-400/20 focus:border-red-400' : 'border-slate-200 dark:border-slate-700 focus:ring-cyan-400/25 focus:border-cyan-500' }} rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none transition"
                    placeholder="Repite tu contraseña"
                >
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-linear-to-r from-purple-600 to-indigo-600 rounded-xl hover:from-purple-500 hover:to-indigo-500 transition-all shadow-md shadow-indigo-500/20 disabled:opacity-70 disabled:cursor-not-allowed">
                <span>Registrarse</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-cyan-600 dark:text-slate-400 dark:hover:text-cyan-400 transition-colors">¿Ya tienes cuenta? Iniciar sesión</a>
            </div>
        </form>
    </section>
</div>
@endsection
