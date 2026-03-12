@extends('layouts.app')

@section('title', 'Acceso restringido')
@section('page-title', 'Usuarios')

@section('content')
    <div class="max-w-3xl">
        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/60 backdrop-blur p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 flex items-center justify-center shadow-lg shadow-rose-500/15">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 11c1.657 0 3-1.343 3-3V7a3 3 0 10-6 0v1c0 1.657 1.343 3 3 3zm6 2H6a2 2 0 00-2 2v4a2 2 0 002 2h12a2 2 0 002-2v-4a2 2 0 00-2-2z" />
                    </svg>
                </div>

                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Necesitas permisos de administrador</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        El apartado <span class="font-semibold">Usuarios</span> esta restringido.
                        Si necesitas acceder, contacta con un administrador para que te indique el procedimiento.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition-all shadow-md shadow-cyan-500/20">
                            Volver al dashboard
                        </a>

                        <a href="{{ route('shop.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 bg-white/70 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Ir a la tienda
                        </a>
                    </div>

                    @auth
                        <p class="mt-5 text-xs text-slate-500 dark:text-slate-400">
                            Sesion iniciada como: <span class="font-mono">{{ auth()->user()->email }}</span>
                        </p>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection
