@extends('layouts.app')

@section('title', 'Token MCP')
@section('page-title', 'Token MCP')

@section('content')
    <div class="max-w-3xl">
        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/60 backdrop-blur p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Token para /mcp/app</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                Genera un token personal (Sanctum) para autenticar el endpoint MCP web. Se rota el token anterior llamado <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800">mcp</code>.
            </p>

            <div class="mt-5">
                <form method="POST" action="{{ route('admin.mcp-token.store') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition-all shadow-md shadow-cyan-500/20">
                        Generar nuevo token
                    </button>
                </form>
            </div>

            @isset($plainTextToken)
                <div class="mt-6 rounded-xl border border-amber-200/70 dark:border-amber-900/60 bg-amber-50/70 dark:bg-amber-950/20 p-4">
                    <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">Copia y guarda este token ahora</p>
                    <p class="mt-1 text-xs text-amber-800/80 dark:text-amber-200/80">Por seguridad, no se podra volver a mostrar una vez salgas de esta pagina.</p>

                    <div class="mt-3">
                        <textarea readonly rows="3" class="w-full font-mono text-xs rounded-xl border border-amber-200 dark:border-amber-900/60 bg-white/80 dark:bg-slate-950/40 p-3 text-slate-900 dark:text-slate-100">{{ $plainTextToken }}</textarea>
                    </div>

                    <p class="mt-3 text-xs text-amber-800/80 dark:text-amber-200/80">
                        Usa: <span class="font-mono">Authorization: Bearer &lt;token&gt;</span>
                    </p>
                </div>
            @endisset
        </div>
    </div>
@endsection
