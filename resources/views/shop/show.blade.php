@extends('layouts.store')

@section('title', $car->brand.' '.$car->model)

@section('content')
<div class="space-y-8">
    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 dark:text-cyan-300 hover:underline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Volver al catálogo
    </a>

    <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-3xl overflow-hidden border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 shadow-sm">
            <a href="{{ route('shop.outbound', $car) }}" target="_blank" rel="noopener noreferrer" class="block">
                <img src="{{ $car->thumbnail_url }}" alt="{{ $car->brand }} {{ $car->model }}" class="w-full h-[360px] sm:h-[440px] object-cover">
            </a>
        </div>

        <aside class="rounded-3xl border border-slate-200/70 dark:border-slate-800/70 bg-white/90 dark:bg-slate-900/85 p-6 shadow-sm backdrop-blur">
            <p class="text-xs font-semibold tracking-widest uppercase text-cyan-700 dark:text-cyan-300">Disponible ahora</p>
            <h1 class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $car->brand }} {{ $car->model }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $car->year }} · {{ $car->city }}</p>
            <p class="mt-2 inline-flex items-center gap-2 rounded-full bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 px-3 py-1 text-xs font-semibold">Origen: {{ $car->source_name }}</p>

            <p class="mt-4 text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">€{{ number_format((float) $car->price, 0, ',', '.') }}</p>

            <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kilometraje</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ number_format($car->mileage, 0, ',', '.') }} km</p>
                </div>
                <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Combustible</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $car->fuel_type }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Transmisión</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $car->transmission }}</p>
                </div>
                <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Color</p>
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $car->color }}</p>
                </div>
            </div>

            <a href="{{ route('shop.outbound', $car) }}" target="_blank" rel="noopener noreferrer" class="mt-6 w-full inline-flex items-center justify-center px-4 py-3 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 transition shadow-md shadow-cyan-500/20">Ir al anuncio oficial</a>

            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Serás redirigido al portal original para continuar la compra.</p>
        </aside>
    </section>

    <section class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/90 dark:bg-slate-900/85 p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Descripción del vehículo</h2>
        <p class="mt-3 leading-relaxed text-slate-600 dark:text-slate-300">{{ $car->description }}</p>
    </section>

    @if($relatedCars->isNotEmpty())
        <section>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Te puede interesar</h2>

            <div class="grid gap-4 md:grid-cols-3">
                @foreach($relatedCars as $relatedCar)
                    <article class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                        <img src="{{ $relatedCar->thumbnail_url }}" alt="{{ $relatedCar->brand }} {{ $relatedCar->model }}" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 dark:text-white">{{ $relatedCar->brand }} {{ $relatedCar->model }}</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $relatedCar->year }} · {{ $relatedCar->city }}</p>
                            <p class="mt-1 text-xs text-cyan-700 dark:text-cyan-300">{{ $relatedCar->source_name }}</p>
                            <p class="mt-3 text-xl font-extrabold text-slate-900 dark:text-white">€{{ number_format((float) $relatedCar->price, 0, ',', '.') }}</p>
                            <a href="{{ route('shop.outbound', $relatedCar) }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex text-sm font-semibold text-cyan-700 dark:text-cyan-300 hover:underline">Ir al anuncio oficial</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
