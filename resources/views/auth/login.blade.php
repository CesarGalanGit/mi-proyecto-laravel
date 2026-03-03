@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
<div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:gap-10 items-start lg:items-center">
    <section class="animate-fade-in space-y-6 lg:pr-6">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/65 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/70 text-slate-700 dark:text-slate-200 backdrop-blur">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Acceso protegido
        </span>

        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Bienvenido de nuevo
            </h1>
            <p class="mt-3 text-slate-600 dark:text-slate-300 leading-relaxed max-w-xl">
                Inicia sesión para gestionar usuarios, exportar datos y mantener el control de tu panel.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/50 px-4 py-3 backdrop-blur">
                <p class="font-semibold text-slate-900 dark:text-white">Bloqueo automático</p>
                <p class="mt-1 text-slate-500 dark:text-slate-400">Protección por intentos fallidos.</p>
            </div>
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/50 px-4 py-3 backdrop-blur">
                <p class="font-semibold text-slate-900 dark:text-white">Sesión segura</p>
                <p class="mt-1 text-slate-500 dark:text-slate-400">Token CSRF y regeneración de sesión.</p>
            </div>
        </div>
    </section>

    <section class="animate-slide-up delay-1 bg-white/85 dark:bg-slate-900/80 rounded-3xl border border-slate-200/70 dark:border-slate-800/70 shadow-xl shadow-slate-950/5 overflow-hidden backdrop-blur">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] uppercase tracking-widest font-semibold text-slate-500 dark:text-slate-400">Portal administrativo</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Laravel Studio</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-600 to-cyan-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        <form id="loginForm" data-login-ui method="POST" action="{{ route('login.store') }}" class="p-6 sm:p-7 space-y-5">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-4" role="alert" aria-live="polite">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-red-700 dark:text-red-400 text-sm">No se pudo iniciar sesión</p>
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
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Correo electrónico</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    inputmode="email"
                    autofocus
                    required
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    aria-describedby="email-error"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('email') ? 'border-red-300 dark:border-red-700 focus:ring-red-400/20 focus:border-red-400' : 'border-slate-200 dark:border-slate-700 focus:ring-cyan-400/25 focus:border-cyan-500' }} rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none transition"
                    placeholder="ejemplo@correo.com"
                >
                @error('email')
                    <p id="email-error" class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Contraseña</label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        aria-describedby="password-error capsLockHint"
                        class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('password') ? 'border-red-300 dark:border-red-700 focus:ring-red-400/20 focus:border-red-400' : 'border-slate-200 dark:border-slate-700 focus:ring-cyan-400/25 focus:border-cyan-500' }} rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none transition"
                        placeholder="Tu contraseña"
                    >
                    <button id="togglePassword" type="button" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-cyan-600 dark:hover:text-cyan-300 transition" aria-controls="password" aria-label="Mostrar contraseña">
                        <svg id="iconEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="iconEyeOff" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.542-7a9.956 9.956 0 012.223-3.592m3.054-2.223A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M15 12a3 3 0 00-4.243-2.829M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p id="password-error" class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p id="capsLockHint" class="hidden text-xs font-medium text-amber-600 dark:text-amber-400">Bloq Mayús está activado.</p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 select-none">
                    <input type="checkbox" name="remember" @checked(old('remember')) class="rounded border-slate-300 dark:border-slate-700 text-cyan-600 focus:ring-cyan-500/30">
                    Recordarme en este equipo
                </label>

                <a href="{{ route('usuarios.index') }}" class="text-sm font-semibold text-cyan-700 dark:text-cyan-300 hover:underline">
                    Volver al listado
                </a>
            </div>

            <button id="loginSubmitButton" type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 rounded-xl hover:from-sky-500 hover:to-cyan-500 transition-all shadow-md shadow-cyan-500/20 disabled:opacity-70 disabled:cursor-not-allowed">
                <svg id="loginSpinner" class="w-4 h-4 hidden animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-90" fill="currentColor" d="M22 12a10 10 0 00-10-10v4a6 6 0 016 6h4z"></path>
                </svg>
                <span id="loginSubmitText" aria-live="polite">Iniciar sesión</span>
                <svg id="loginArrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </form>

        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <p class="text-xs text-slate-500 dark:text-slate-400">Autenticación protegida con límite de intentos y sesión regenerada.</p>
            <p class="text-xs text-slate-400 dark:text-slate-500">Laravel Studio</p>
        </div>
    </section>
</div>
@endsection
