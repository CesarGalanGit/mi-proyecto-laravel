@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ============================================
         TOOLBAR: Search + Actions
         ============================================ --}}
    <div class="animate-fade-in flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        {{-- Search --}}
        <form action="{{ route('usuarios.index') }}" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text"
                       name="buscar"
                       value="{{ request('buscar') }}"
                       placeholder="Buscar por nombre o email..."
                       class="search-input w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:border-indigo-400 dark:focus:border-indigo-500">
                {{-- Preserve per_page in search --}}
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
            </div>
        </form>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3">
            @can('manage-users')
                <a href="{{ route('usuarios.export') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    CSV
                </a>

                <button onclick="openModal()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-xl hover:from-indigo-500 hover:to-violet-500 transition-all shadow-md shadow-indigo-500/25 hover:shadow-lg hover:shadow-indigo-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Usuario
                </button>
            @else
                @auth
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white/70 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 10-8 0v4" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 11h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                        </svg>
                        Sin permisos
                    </span>
                @endauth

                @guest
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-500 dark:text-slate-400">
                        Inicia sesion desde la barra superior para gestionar.
                    </span>
                @endguest
            @endcan
        </div>
    </div>

    {{-- ============================================
         VALIDATION ERRORS (from store/update)
         ============================================ --}}
    @if ($errors->any())
        <div class="animate-fade-in bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-red-700 dark:text-red-400 text-sm">¡Revisa los siguientes datos:</p>
                    <ul class="mt-1 text-sm text-red-600 dark:text-red-400/80 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================
         USERS TABLE
         ============================================ --}}
    <div class="animate-slide-up delay-1 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 shadow-sm overflow-hidden">

        {{-- Table Header Info --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-semibold text-slate-800 dark:text-white">Listado de Usuarios</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $usuarios->total() }} usuario(s) encontrado(s)</p>
            </div>
            {{-- Items per page selector --}}
            <form action="{{ route('usuarios.index') }}" method="GET" class="flex items-center gap-2">
                @if(request('buscar'))
                    <input type="hidden" name="buscar" value="{{ request('buscar') }}">
                @endif
                <label class="text-xs text-slate-500 dark:text-slate-400">Mostrar:</label>
                <select name="per_page" onchange="this.form.submit()"
                        class="text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-slate-700 dark:text-slate-300 outline-none focus:ring-2 focus:ring-indigo-400/30">
                    @foreach([5, 10, 25] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 5) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Usuario</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:table-cell">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell">Registro</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($usuarios as $index => $user)
                        <tr class="table-row-animate hover:bg-slate-50/80 dark:hover:bg-slate-800/40" id="user-row-{{ $user->id }}">
                            {{-- User info with avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $colors = ['bg-indigo-500', 'bg-emerald-500', 'bg-violet-500', 'bg-amber-500', 'bg-rose-500', 'bg-cyan-500', 'bg-fuchsia-500', 'bg-teal-500'];
                                        $avatarColor = $colors[$user->id % count($colors)];
                                        $initials = collect(explode(' ', $user->name))->map(fn($word) => mb_substr($word, 0, 1))->take(2)->implode('');
                                    @endphp
                                    <div class="avatar w-9 h-9 text-xs text-white {{ $avatarColor }} shadow-sm">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-slate-500 dark:text-slate-400 sm:hidden text-xs">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 hidden sm:table-cell">
                                {{ $user->email }}
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $user->created_at->format('d/m/Y') }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @can('manage-users')
                                        {{-- Edit button --}}
                                        <button data-user-id="{{ $user->id }}" onclick="toggleEdit(this.dataset.userId)"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:text-indigo-400 dark:hover:bg-indigo-950/50 transition"
                                                title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Delete button --}}
                                        <form action="{{ route('usuarios.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('¿Seguro que quieres eliminar a {{ addslashes($user->name) }}? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-950/50 transition"
                                                    title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 10-8 0v4" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 11h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                                            </svg>
                                            Bloqueado
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        @can('manage-users')
                            {{-- Inline Edit Row (hidden by default) --}}
                            <tr id="edit-row-{{ $user->id }}" class="hidden bg-indigo-50/50 dark:bg-indigo-950/20">
                                <td colspan="4" class="px-6 py-4">
                                    <form action="{{ route('usuarios.update', $user) }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
                                            <input type="text" name="nombre" value="{{ $user->name }}"
                                                   class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400"
                                                   placeholder="Nombre completo" required>
                                            <input type="email" name="correo" value="{{ $user->email }}"
                                                   class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400"
                                                   placeholder="Email" required>
                                            <input type="password" name="password"
                                                   class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400"
                                                   placeholder="Nueva contraseña (opcional)">
                                            <input type="password" name="password_confirmation"
                                                   class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400"
                                                   placeholder="Confirmar contraseña">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-500 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Guardar
                                            </button>
                                            <button type="button" data-user-id="{{ $user->id }}" onclick="toggleEdit(this.dataset.userId)"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endcan
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-slate-500 dark:text-slate-400">No hay usuarios</p>
                                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Empieza creando el primero</p>
                    @can('manage-users')
                        <button onclick="openModal()" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-950 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Crear usuario
                                    </button>
                    @else
                        @auth
                            <span class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-white/70 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V7a4 4 0 10-8 0v4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 11h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                                </svg>
                                Sin permisos
                            </span>
                        @endauth

                        @guest
                            <a href="{{ route('login') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-lg hover:from-indigo-500 hover:to-violet-500 transition-all shadow-md shadow-indigo-500/25">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17l5-5m0 0l-5-5m5 5H3" />
                                </svg>
                                Iniciar sesion
                            </a>
                        @endguest
                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($usuarios->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ============================================
     CREATE USER MODAL
     ============================================ --}}
@can('manage-users')
<div id="createModal" class="modal-backdrop" data-auto-open="{{ $errors->any() && old('nombre') !== null ? '1' : '0' }}" onclick="if(event.target === this) closeModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="modal-panel w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Nuevo Usuario</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Completa los datos para crear un usuario</p>
                </div>
                <button onclick="closeModal()"
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form action="{{ route('usuarios.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="modal-nombre" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Nombre completo
                    </label>
                    <input type="text" id="modal-nombre" name="nombre" value="{{ old('nombre') }}"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition"
                           placeholder="Ej: Ana García López" required>
                </div>

                <div>
                    <label for="modal-correo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Correo electrónico
                    </label>
                    <input type="email" id="modal-correo" name="correo" value="{{ old('correo') }}"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition"
                           placeholder="ana@ejemplo.com" required>
                </div>

                <div>
                    <label for="modal-password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Contraseña
                    </label>
                    <input type="password" id="modal-password" name="password"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition"
                           placeholder="Mínimo 8 caracteres" required>
                </div>

                <div>
                    <label for="modal-password-confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Confirmar contraseña
                    </label>
                    <input type="password" id="modal-password-confirmation" name="password_confirmation"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition"
                           placeholder="Repite la contraseña" required>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-xl hover:from-indigo-500 hover:to-violet-500 transition-all shadow-md shadow-indigo-500/25">
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('scripts')
<script>
    // ---- Modal ----
    function openModal() {
        document.getElementById('createModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        // Focus first input after animation
        setTimeout(() => document.getElementById('modal-nombre').focus(), 300);
    }

    function closeModal() {
        document.getElementById('createModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    // ---- Inline Edit Toggle ----
    function toggleEdit(userId) {
        const editRow = document.getElementById('edit-row-' + userId);
        const userRow = document.getElementById('user-row-' + userId);

        editRow.classList.toggle('hidden');

        // Visual feedback on the original row
        if (!editRow.classList.contains('hidden')) {
            userRow.classList.add('opacity-50');
        } else {
            userRow.classList.remove('opacity-50');
        }
    }

    // Auto-open modal if there are old input errors for the create form
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('createModal');
        if (modal && modal.dataset.autoOpen === '1') {
            openModal();
        }
    });
</script>
@endsection
