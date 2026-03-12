@extends('layouts.guest')

@section('title', 'Verificar correo')

@section('content')
<div class="max-w-md mx-auto">
    <section class="animate-slide-up bg-white/85 dark:bg-slate-900/80 rounded-3xl border border-slate-200/70 dark:border-slate-800/70 shadow-xl shadow-slate-950/5 overflow-hidden backdrop-blur">
        <div class="p-6 sm:p-7 space-y-6">
            <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-500/20 mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Verifica tu correo electrónico
                </h1>
                <p class="mt-3 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                    ¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos otro.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
                    <p class="font-medium text-emerald-700 dark:text-emerald-400 text-sm">
                        Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.
                    </p>
                </div>
            @endif

            <div class="flex items-center justify-between gap-4 pt-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm">
                        Reenviar correo de verificación
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors underline underline-offset-4 decoration-slate-300 dark:decoration-slate-600">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
