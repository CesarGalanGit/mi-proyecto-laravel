@extends('layouts.app')

@section('title', 'Admin Tienda - Coches')
@section('page-title', 'Admin Tienda / Coches')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 p-5 sm:p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Nuevo coche</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Añade vehículos al catálogo de la tienda.</p>

        <form method="POST" action="{{ route('admin.shop.cars.store') }}" class="mt-5 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @csrf

            <input name="brand" value="{{ old('brand') }}" placeholder="Marca" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
            <input name="model" value="{{ old('model') }}" placeholder="Modelo" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
            <input type="number" name="year" value="{{ old('year') }}" placeholder="Año" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

            <input type="number" step="0.01" name="price" value="{{ old('price') }}" placeholder="Precio" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
            <input type="number" name="mileage" value="{{ old('mileage') }}" placeholder="Kilometraje" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
            <input name="city" value="{{ old('city') }}" placeholder="Ciudad" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

            <select name="fuel_type" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                @foreach(['Gasolina', 'Diésel', 'Híbrido', 'Eléctrico'] as $fuelType)
                    <option value="{{ $fuelType }}" @selected(old('fuel_type') === $fuelType)>{{ $fuelType }}</option>
                @endforeach
            </select>

            <select name="transmission" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                @foreach(['Automática', 'Manual'] as $transmission)
                    <option value="{{ $transmission }}" @selected(old('transmission') === $transmission)>{{ $transmission }}</option>
                @endforeach
            </select>

            <select name="status" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                @foreach(['available' => 'Disponible', 'reserved' => 'Reservado', 'sold' => 'Vendido'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', 'available') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="source_name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                @foreach(['Wallapop', 'Coches.net', 'AutoScout24', 'Milanuncios', 'Otro portal'] as $sourceName)
                    <option value="{{ $sourceName }}" @selected(old('source_name') === $sourceName)>{{ $sourceName }}</option>
                @endforeach
            </select>

            <input type="url" name="source_url" value="{{ old('source_url') }}" placeholder="URL anuncio oficial" class="md:col-span-2 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

            <input name="color" value="{{ old('color') }}" placeholder="Color" class="md:col-span-2 lg:col-span-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
            <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url') }}" placeholder="URL imagen principal" class="md:col-span-2 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

            <textarea name="gallery_urls" rows="3" placeholder="URLs de galería (una por línea)" class="md:col-span-2 lg:col-span-3 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">{{ old('gallery_urls') }}</textarea>
            <textarea name="description" rows="3" placeholder="Descripción comercial" class="md:col-span-2 lg:col-span-3 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">{{ old('description') }}</textarea>

            <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 md:col-span-2 lg:col-span-1">
                <input type="hidden" name="featured" value="0">
                <input type="checkbox" name="featured" value="1" @checked(old('featured')) class="rounded border-slate-300 dark:border-slate-700 text-cyan-600">
                Destacar en catálogo
            </label>

            <div class="md:col-span-2 lg:col-span-2 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-linear-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition shadow-md shadow-cyan-500/20">Crear coche</button>
            </div>
        </form>

        @if($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/40 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                {{ $errors->first() }}
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 dark:border-slate-800/70">
            <form method="GET" action="{{ route('admin.shop.cars.index') }}" class="grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Buscar marca, modelo, ciudad o slug" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                <select name="status" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                    <option value="">Todos los estados</option>
                    <option value="available" @selected($filters['status'] === 'available')>Disponible</option>
                    <option value="reserved" @selected($filters['status'] === 'reserved')>Reservado</option>
                    <option value="sold" @selected($filters['status'] === 'sold')>Vendido</option>
                </select>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 dark:bg-slate-100 dark:text-slate-900">Filtrar</button>
            </form>
        </div>

        <div class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
            @forelse($cars as $car)
                <details class="group">
                    <summary class="list-none cursor-pointer px-5 sm:px-6 py-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $car->thumbnail_url }}" alt="{{ $car->brand }} {{ $car->model }}" class="w-20 h-14 rounded-lg object-cover">
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $car->brand }} {{ $car->model }} ({{ $car->year }})</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $car->city }} · {{ number_format($car->mileage, 0, ',', '.') }} km · {{ $car->fuel_type }}</p>
                                    <p class="text-xs text-cyan-700 dark:text-cyan-300 mt-1">Origen: {{ $car->source_name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Clics salientes: {{ number_format((int) $car->outbound_clicks, 0, ',', '.') }} · Sync: {{ optional($car->last_synced_at)->format('d/m/Y H:i') ?? '---' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $car->status === 'available' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ($car->status === 'reserved' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200') }}">{{ $car->status }}</span>
                                <span class="text-lg font-extrabold text-slate-900 dark:text-white">€{{ number_format((float) $car->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </summary>

                    <div class="px-5 sm:px-6 pb-5">
                        <form method="POST" action="{{ route('admin.shop.cars.update', $car) }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 pt-2">
                            @csrf
                            @method('PUT')

                            <input name="brand" value="{{ $car->brand }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                            <input name="model" value="{{ $car->model }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                            <input type="number" name="year" value="{{ $car->year }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

                            <input type="number" step="0.01" name="price" value="{{ $car->price }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                            <input type="number" name="mileage" value="{{ $car->mileage }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                            <input name="city" value="{{ $car->city }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

                            <select name="fuel_type" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                                @foreach(['Gasolina', 'Diésel', 'Híbrido', 'Eléctrico'] as $fuelType)
                                    <option value="{{ $fuelType }}" @selected($car->fuel_type === $fuelType)>{{ $fuelType }}</option>
                                @endforeach
                            </select>

                            <select name="transmission" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                                @foreach(['Automática', 'Manual'] as $transmission)
                                    <option value="{{ $transmission }}" @selected($car->transmission === $transmission)>{{ $transmission }}</option>
                                @endforeach
                            </select>

                            <select name="status" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                                <option value="available" @selected($car->status === 'available')>Disponible</option>
                                <option value="reserved" @selected($car->status === 'reserved')>Reservado</option>
                                <option value="sold" @selected($car->status === 'sold')>Vendido</option>
                            </select>

                            <select name="source_name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                                @foreach(['Wallapop', 'Coches.net', 'AutoScout24', 'Milanuncios', 'Otro portal'] as $sourceName)
                                    <option value="{{ $sourceName }}" @selected($car->source_name === $sourceName)>{{ $sourceName }}</option>
                                @endforeach
                            </select>

                            <input type="url" name="source_url" value="{{ $car->source_url }}" class="md:col-span-2 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

                            <div class="md:col-span-2 lg:col-span-3 flex justify-end">
                                <a href="{{ route('shop.outbound', $car) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800 bg-cyan-50 dark:bg-cyan-950/30 hover:bg-cyan-100 dark:hover:bg-cyan-950/50 transition">Ver anuncio oficial</a>
                            </div>

                            <input name="color" value="{{ $car->color }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">
                            <input type="url" name="thumbnail_url" value="{{ $car->thumbnail_url }}" class="md:col-span-2 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">

                            <textarea name="gallery_urls" rows="3" class="md:col-span-2 lg:col-span-3 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">{{ collect($car->gallery ?? [])->implode("\n") }}</textarea>
                            <textarea name="description" rows="3" class="md:col-span-2 lg:col-span-3 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm">{{ $car->description }}</textarea>

                            <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 md:col-span-2 lg:col-span-1">
                                <input type="hidden" name="featured" value="0">
                                <input type="checkbox" name="featured" value="1" @checked($car->featured) class="rounded border-slate-300 dark:border-slate-700 text-cyan-600">
                                Destacado
                            </label>

                            <div class="md:col-span-2 lg:col-span-2 flex flex-wrap items-center justify-end gap-2">
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-linear-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition">Guardar cambios</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.shop.cars.destroy', $car) }}" class="mt-3 flex justify-end" onsubmit="return confirm('¿Eliminar este coche del catálogo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-950/60 transition">Eliminar</button>
                        </form>
                    </div>
                </details>
            @empty
                <div class="px-5 sm:px-6 py-12 text-center text-slate-500 dark:text-slate-400">No hay coches para mostrar.</div>
            @endforelse
        </div>

        @if($cars->hasPages())
            <div class="px-5 sm:px-6 py-4 border-t border-slate-200/70 dark:border-slate-800/70">
                {{ $cars->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
