@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Header de bienvenida --}}
    <div class="animate-fade-in">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
            Bienvenido a <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">Laravel Studio</span>
        </h1>
        <p class="mt-2 text-slate-500 dark:text-slate-400">
            Tu proyecto de estudio PHP/Laravel. Aquí tienes un resumen de tu base de datos.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- Total Usuarios --}}
        <div class="stat-card animate-slide-up delay-1 bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/70 dark:border-slate-800/70 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Usuarios</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900 dark:text-white">{{ $totalUsuarios ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <a href="/usuarios" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Ver todos →</a>
            </div>
        </div>

        {{-- Último Usuario --}}
        <div class="stat-card animate-slide-up delay-2 bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/70 dark:border-slate-800/70 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Último Registro</p>
                    <p class="mt-2 text-xl font-bold text-slate-900 dark:text-white truncate max-w-[180px]">
                        {{ $ultimoUsuario->name ?? 'Sin datos' }}
                    </p>
                    <p class="mt-1 text-sm text-slate-400 dark:text-slate-500 truncate max-w-[180px]">
                        {{ $ultimoUsuario->email ?? '—' }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    @if($ultimoUsuario ?? false)
                        Registrado {{ $ultimoUsuario->created_at->diffForHumans() }}
                    @else
                        Sin registros aún
                    @endif
                </p>
            </div>
        </div>

        {{-- Usuarios Hoy --}}
        <div class="stat-card animate-slide-up delay-3 bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/70 dark:border-slate-800/70 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Registrados Hoy</p>
                    <p class="mt-2 text-4xl font-extrabold text-slate-900 dark:text-white">{{ $usuariosHoy ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shadow-lg shadow-violet-500/25">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-300">
                    {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="animate-slide-up delay-4">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <a href="/usuarios"
               class="group flex items-center gap-4 p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all duration-300">
                <div class="w-11 h-11 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 dark:text-white">Gestionar Usuarios</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Crear, editar y eliminar</p>
                </div>
            </a>

            <a href="/usuarios/exportar"
               class="group flex items-center gap-4 p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 shadow-sm hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 dark:text-white">Exportar CSV</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Descargar todos los datos</p>
                </div>
            </a>

            <a href="https://laravel.com/docs" target="_blank"
               class="group flex items-center gap-4 p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 shadow-sm hover:shadow-md hover:border-violet-300 dark:hover:border-violet-700 transition-all duration-300">
                <div class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 dark:text-white">Documentación</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Aprender más sobre Laravel</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection
