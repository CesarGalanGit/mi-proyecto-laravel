<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brand = fake()->randomElement(['Toyota', 'BMW', 'Audi', 'Mercedes-Benz', 'Tesla', 'Porsche', 'Mazda', 'Volvo']);
        $model = fake()->randomElement(['Corolla', 'Serie 3', 'A4', 'Clase C', 'Model 3', 'Macan', 'CX-5', 'XC60']);
        $year = fake()->numberBetween(2018, 2025);
        $name = $brand.' '.$model.' '.$year;
        $sourceName = fake()->randomElement(['Wallapop', 'Coches.net', 'AutoScout24', 'Milanuncios']);
        $sourceBaseUrl = match ($sourceName) {
            'Wallapop' => 'https://es.wallapop.com/item',
            'Coches.net' => 'https://www.coches.net/coches-segunda-mano',
            'AutoScout24' => 'https://www.autoscout24.es/anuncios',
            default => 'https://www.milanuncios.com/coches-de-segunda-mano',
        };

        return [
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(100, 999)),
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'price' => fake()->numberBetween(18500, 92000),
            'mileage' => fake()->numberBetween(3000, 105000),
            'fuel_type' => fake()->randomElement(['Gasolina', 'Diésel', 'Híbrido', 'Eléctrico']),
            'transmission' => fake()->randomElement(['Automática', 'Manual']),
            'color' => fake()->randomElement(['Negro', 'Blanco', 'Azul', 'Gris', 'Rojo']),
            'city' => fake()->randomElement(['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao']),
            'featured' => fake()->boolean(30),
            'status' => 'available',
            'source_name' => $sourceName,
            'source_external_id' => (string) fake()->unique()->numberBetween(100000, 999999),
            'source_url' => $sourceBaseUrl.'/'.Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'outbound_clicks' => 0,
            'last_synced_at' => now(),
            'thumbnail_url' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=900&q=80',
            'gallery' => [
                'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=1200&q=80',
            ],
            'description' => fake()->sentence(18),
        ];
    }
}
