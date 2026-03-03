@extends('layouts.app')

@section('title', 'Admin Tienda - Pedidos')
@section('page-title', 'Admin Tienda / Pedidos')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800/70">
            <form method="GET" action="{{ route('admin.shop.orders.index') }}" class="grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Pedido, cliente o email" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                <select name="status" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                    <option value="">Todos los estados</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Pendiente</option>
                    <option value="confirmed" @selected($filters['status'] === 'confirmed')>Confirmado</option>
                    <option value="completed" @selected($filters['status'] === 'completed')>Completado</option>
                    <option value="cancelled" @selected($filters['status'] === 'cancelled')>Cancelado</option>
                </select>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 dark:bg-slate-100 dark:text-slate-900">Filtrar</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60">
                        <th class="text-left px-5 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pedido</th>
                        <th class="text-left px-5 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cliente</th>
                        <th class="text-left px-5 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Vehículos</th>
                        <th class="text-right px-5 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
                        <th class="text-right px-5 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-5 sm:px-6 py-4 align-top">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ optional($order->placed_at)->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Usuario: {{ $order->user?->email ?? 'Invitado' }}</p>
                            </td>
                            <td class="px-5 sm:px-6 py-4 align-top">
                                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $order->customer_name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $order->customer_email }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $order->customer_phone }}</p>
                            </td>
                            <td class="px-5 sm:px-6 py-4 align-top">
                                <p class="text-slate-600 dark:text-slate-300">{{ $order->items->pluck('car_name')->implode(', ') }}</p>
                            </td>
                            <td class="px-5 sm:px-6 py-4 align-top text-right">
                                <p class="font-extrabold text-slate-900 dark:text-white">€{{ number_format((float) $order->total, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-5 sm:px-6 py-4 align-top text-right">
                                <form method="POST" action="{{ route('admin.shop.orders.update', $order) }}" class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-xs font-semibold">
                                        <option value="pending" @selected($order->status === 'pending')>Pendiente</option>
                                        <option value="confirmed" @selected($order->status === 'confirmed')>Confirmado</option>
                                        <option value="completed" @selected($order->status === 'completed')>Completado</option>
                                        <option value="cancelled" @selected($order->status === 'cancelled')>Cancelado</option>
                                    </select>
                                    <button type="submit" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition">Guardar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 sm:px-6 py-12 text-center text-slate-500 dark:text-slate-400">No hay pedidos para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-5 sm:px-6 py-4 border-t border-slate-200/70 dark:border-slate-800/70">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
