@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="animate-fade-in">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/60 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/60 text-slate-700 dark:text-slate-200 backdrop-blur">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Acceso seguro
        </span>

        <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            Iniciar sesión
        </h1>
        <p class="mt-2 text-slate-600 dark:text-slate-300 leading-relaxed">
            Para crear, editar, eliminar o exportar usuarios necesitas autenticarte.
        </p>
    </div>

    <div class="animate-slide-up delay-1 bg-white/80 dark:bg-slate-900/75 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 shadow-sm overflow-hidden backdrop-blur">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
            <p class="text-[11px] uppercase tracking-widest font-semibold text-slate-500 dark:text-slate-400">Portal de administración</p>
            <div class="mt-2 flex items-center justify-between gap-4">
                <p class="text-lg font-semibold text-slate-900 dark:text-white">Laravel Studio</p>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        <form id="loginForm" method="POST" action="{{ route('login.store') }}" class="p-6 sm:p-7 space-y-6">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-4">
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
                    required
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition"
                    placeholder="ejemplo@correo.com"
                >
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
                        class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition"
                        placeholder="Tu contraseña"
                    >
                    <button id="togglePassword" type="button" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 transition" aria-label="Mostrar contraseña">
                        <svg id="iconEye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="iconEyeOff" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.542-7a9.956 9.956 0 012.223-3.592m3.054-2.223A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411M15 12a3 3 0 00-4.243-2.829M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 select-none">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500/30">
                    Mantener sesión
                </label>

                <a href="{{ route('usuarios.index') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-300 hover:underline">
                    Volver al listado
                </a>
            </div>

            <button id="loginSubmitButton" type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-xl hover:from-indigo-500 hover:to-violet-500 transition-all shadow-md shadow-indigo-500/25 disabled:opacity-70 disabled:cursor-not-allowed">
                <svg id="loginSpinner" class="w-4 h-4 hidden animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-90" fill="currentColor" d="M22 12a10 10 0 00-10-10v4a6 6 0 016 6h4z"></path>
                </svg>
                <span id="loginSubmitText">Iniciar sesión</span>
                <svg id="loginArrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </form>

        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <p class="text-xs text-slate-500 dark:text-slate-400">Sesión regenerada y protegida por límite de intentos.</p>
            <p class="text-xs text-slate-400 dark:text-slate-500">Laravel Studio</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const iconEye = document.getElementById('iconEye');
        const iconEyeOff = document.getElementById('iconEyeOff');
        const loginForm = document.getElementById('loginForm');
        const submitButton = document.getElementById('loginSubmitButton');
        const submitText = document.getElementById('loginSubmitText');
        const submitArrow = document.getElementById('loginArrow');
        const spinner = document.getElementById('loginSpinner');

        togglePassword?.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            iconEye.classList.toggle('hidden', isHidden);
            iconEyeOff.classList.toggle('hidden', !isHidden);
            togglePassword.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });

        loginForm?.addEventListener('submit', function () {
            submitButton.disabled = true;
            submitText.textContent = 'Validando...';
            submitArrow.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    });
</script>
@endsection
