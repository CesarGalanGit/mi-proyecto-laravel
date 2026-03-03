@extends('layouts.store')

@section('title', 'Tienda de coches')

@section('content')
<div class="space-y-8">
    <section class="relative overflow-hidden rounded-3xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/70 backdrop-blur">
        <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-cyan-500/20 to-transparent"></div>
        <div class="relative px-6 sm:px-10 py-10 sm:py-14 grid gap-8 lg:grid-cols-[1.2fr_0.8fr] items-center">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 px-3 py-1 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Stock revisado y certificado
                </span>
                <h1 class="mt-4 text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white">Encuentra tu próximo coche hoy</h1>
                <p class="mt-3 max-w-2xl text-slate-600 dark:text-slate-300 leading-relaxed">Escaparate profesional con anuncios enlazados a Wallapop, Coches.net y otros portales oficiales para cerrar la compra directamente en origen.</p>
                <div class="mt-6 flex flex-wrap gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <span class="px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800">Anuncios verificados</span>
                    <span class="px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800">Enlace oficial</span>
                    <span class="px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800">Comparativa rápida</span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-slate-950 text-white p-6 shadow-lg">
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-300">Coches destacados</p>
                <p class="mt-2 text-3xl font-extrabold">{{ $featuredCars->count() }}</p>
                <p class="mt-2 text-sm text-slate-300">Modelos premium listos para consultar en su portal oficial.</p>
                <a href="#catalogo" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-cyan-300 hover:text-cyan-200 transition">
                    Ver catálogo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/70 p-5 sm:p-6 backdrop-blur">
        <form method="GET" action="{{ route('shop.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-7">
            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Marca, modelo o ciudad" class="lg:col-span-2 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-cyan-400/30">

            <select name="fuel" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-cyan-400/30">
                <option value="">Combustible</option>
                @foreach(['Gasolina', 'Diésel', 'Híbrido', 'Eléctrico'] as $fuelType)
                    <option value="{{ $fuelType }}" @selected($filters['fuel'] === $fuelType)>{{ $fuelType }}</option>
                @endforeach
            </select>

            <select name="transmission" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-cyan-400/30">
                <option value="">Transmisión</option>
                @foreach(['Automática', 'Manual'] as $transmission)
                    <option value="{{ $transmission }}" @selected($filters['transmission'] === $transmission)>{{ $transmission }}</option>
                @endforeach
            </select>

            <select name="source" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-cyan-400/30">
                <option value="">Portal</option>
                @foreach(['Wallapop', 'Coches.net', 'AutoScout24', 'Milanuncios', 'Otro portal'] as $sourceName)
                    <option value="{{ $sourceName }}" @selected($filters['source'] === $sourceName)>{{ $sourceName }}</option>
                @endforeach
            </select>

            <select name="max_price" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-cyan-400/30">
                <option value="0">Precio máximo</option>
                @foreach([25000, 40000, 60000, 90000] as $price)
                    <option value="{{ $price }}" @selected((int) $filters['max_price'] === $price)>Hasta €{{ number_format($price, 0, ',', '.') }}</option>
                @endforeach
            </select>

            <select name="sort" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-cyan-400/30">
                <option value="latest" @selected($filters['sort'] === 'latest')>Más recientes</option>
                <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Precio: menor a mayor</option>
                <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Precio: mayor a menor</option>
                <option value="mileage_asc" @selected($filters['sort'] === 'mileage_asc')>Menor kilometraje</option>
            </select>

            <div class="sm:col-span-2 lg:col-span-7 flex gap-2">
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition shadow-md shadow-cyan-500/20">Aplicar filtros</button>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Limpiar</a>
            </div>
        </form>
    </section>

    @if($featuredCars->isNotEmpty())
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Selección destacada</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Modelos recomendados por el equipo</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                @foreach($featuredCars as $featuredCar)
                    <article class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                        <a href="{{ route('shop.outbound', $featuredCar) }}" target="_blank" rel="noopener noreferrer" class="block">
                            <img src="{{ $featuredCar->thumbnail_url }}" alt="{{ $featuredCar->brand }} {{ $featuredCar->model }}" class="w-full h-44 object-cover">
                        </a>
                        <div class="p-4">
                            <p class="text-xs font-semibold text-cyan-700 dark:text-cyan-300">Destacado</p>
                            <h3 class="mt-1 font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('shop.outbound', $featuredCar) }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $featuredCar->brand }} {{ $featuredCar->model }}</a>
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $featuredCar->source_name }}</p>
                            <p class="mt-2 text-xl font-extrabold text-slate-900 dark:text-white">€{{ number_format((float) $featuredCar->price, 0, ',', '.') }}</p>
                            <a href="{{ route('shop.outbound', $featuredCar) }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-sm font-semibold text-cyan-700 dark:text-cyan-300 hover:underline">Ir al anuncio oficial</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section id="catalogo">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Catálogo disponible</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $cars->total() }} coche(s)</p>
        </div>

        @if($cars->count() > 0)
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach($cars as $car)
                    <article class="group rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 overflow-hidden shadow-sm hover:shadow-lg transition">
                        <a href="{{ route('shop.outbound', $car) }}" target="_blank" rel="noopener noreferrer" class="block relative overflow-hidden">
                            <img src="{{ $car->thumbnail_url }}" alt="{{ $car->brand }} {{ $car->model }}" class="w-full h-52 object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/90 text-slate-700">
                                {{ $car->year }}
                            </div>
                        </a>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-slate-900 dark:text-white">
                                        <a href="{{ route('shop.outbound', $car) }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $car->brand }} {{ $car->model }}</a>
                                    </h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $car->city }}</p>
                                    <p class="text-xs text-cyan-700 dark:text-cyan-300 mt-1">{{ $car->source_name }}</p>
                                </div>
                                <p class="text-xl font-extrabold text-slate-900 dark:text-white">€{{ number_format((float) $car->price, 0, ',', '.') }}</p>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-2 text-xs text-slate-500 dark:text-slate-400">
                                <div class="rounded-lg bg-slate-100 dark:bg-slate-800 px-2 py-1.5 text-center">{{ number_format($car->mileage, 0, ',', '.') }} km</div>
                                <div class="rounded-lg bg-slate-100 dark:bg-slate-800 px-2 py-1.5 text-center">{{ $car->fuel_type }}</div>
                                <div class="rounded-lg bg-slate-100 dark:bg-slate-800 px-2 py-1.5 text-center">{{ $car->transmission }}</div>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('shop.show', $car) }}" class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Detalle</a>
                                <a href="{{ route('shop.outbound', $car) }}" target="_blank" rel="noopener noreferrer" class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition shadow-md shadow-cyan-500/20">Ver anuncio oficial</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $cars->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/70 p-10 text-center">
                <p class="text-lg font-semibold text-slate-700 dark:text-slate-200">No encontramos coches con esos filtros.</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Prueba con otros criterios o limpia la búsqueda.</p>
            </div>
        @endif
    </section>
</div>
@endsection
