<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                'brand' => 'BMW',
                'model' => 'Serie 3 320d',
                'year' => 2022,
                'price' => 38990,
                'mileage' => 31000,
                'fuel_type' => 'Diésel',
                'transmission' => 'Automática',
                'color' => 'Negro metalizado',
                'city' => 'Madrid',
                'featured' => true,
                'source_name' => 'Coches.net',
                'source_url' => 'https://www.coches.net/bmw-serie-3-320d-2022-madrid-12345678-covo.aspx',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'brand' => 'Audi',
                'model' => 'A4 35 TFSI',
                'year' => 2021,
                'price' => 32900,
                'mileage' => 42000,
                'fuel_type' => 'Gasolina',
                'transmission' => 'Automática',
                'color' => 'Gris Daytona',
                'city' => 'Barcelona',
                'featured' => true,
                'source_name' => 'Wallapop',
                'source_url' => 'https://es.wallapop.com/item/audi-a4-35-tfsi-2021-1034567890',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'brand' => 'Tesla',
                'model' => 'Model 3 Long Range',
                'year' => 2023,
                'price' => 45990,
                'mileage' => 19000,
                'fuel_type' => 'Eléctrico',
                'transmission' => 'Automática',
                'color' => 'Blanco perla',
                'city' => 'Valencia',
                'featured' => true,
                'source_name' => 'AutoScout24',
                'source_url' => 'https://www.autoscout24.es/anuncios/tesla-model-3-long-range-2023-valencia-abc123',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'brand' => 'Mercedes-Benz',
                'model' => 'GLC 220d',
                'year' => 2020,
                'price' => 41900,
                'mileage' => 58000,
                'fuel_type' => 'Diésel',
                'transmission' => 'Automática',
                'color' => 'Azul cavansita',
                'city' => 'Sevilla',
                'featured' => false,
                'source_name' => 'Milanuncios',
                'source_url' => 'https://www.milanuncios.com/coches-de-segunda-mano/mercedes-glc-220d-sevilla-123456789.htm',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'brand' => 'Porsche',
                'model' => 'Macan S',
                'year' => 2022,
                'price' => 74990,
                'mileage' => 22000,
                'fuel_type' => 'Gasolina',
                'transmission' => 'Automática',
                'color' => 'Rojo carmín',
                'city' => 'Bilbao',
                'featured' => true,
                'source_name' => 'Coches.net',
                'source_url' => 'https://www.coches.net/porsche-macan-s-2022-bilbao-22334455-covo.aspx',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1611821064430-0d40291d0f0b?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'brand' => 'Volvo',
                'model' => 'XC60 B4',
                'year' => 2021,
                'price' => 39900,
                'mileage' => 47000,
                'fuel_type' => 'Híbrido',
                'transmission' => 'Automática',
                'color' => 'Gris plata',
                'city' => 'Málaga',
                'featured' => false,
                'source_name' => 'Wallapop',
                'source_url' => 'https://es.wallapop.com/item/volvo-xc60-b4-2021-1045678912',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'brand' => 'Mazda',
                'model' => 'CX-5 Zenith',
                'year' => 2019,
                'price' => 25900,
                'mileage' => 69000,
                'fuel_type' => 'Gasolina',
                'transmission' => 'Manual',
                'color' => 'Azul oscuro',
                'city' => 'Zaragoza',
                'featured' => false,
                'source_name' => 'AutoScout24',
                'source_url' => 'https://www.autoscout24.es/anuncios/mazda-cx-5-zenith-2019-zaragoza-def456',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'brand' => 'Toyota',
                'model' => 'RAV4 Hybrid',
                'year' => 2024,
                'price' => 42990,
                'mileage' => 8000,
                'fuel_type' => 'Híbrido',
                'transmission' => 'Automática',
                'color' => 'Blanco lunar',
                'city' => 'Alicante',
                'featured' => true,
                'source_name' => 'Milanuncios',
                'source_url' => 'https://www.milanuncios.com/coches-de-segunda-mano/toyota-rav4-hybrid-2024-alicante-456789123.htm',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1590362891991-f776e747a588?auto=format&fit=crop&w=900&q=80',
            ],
        ];

        foreach ($cars as $car) {
            Car::query()->updateOrCreate(
                ['slug' => Str::slug($car['brand'].'-'.$car['model'].'-'.$car['year'])],
                [
                    ...$car,
                    'slug' => Str::slug($car['brand'].'-'.$car['model'].'-'.$car['year']),
                    'source_external_id' => md5($car['source_url']),
                    'status' => 'available',
                    'outbound_clicks' => 0,
                    'last_synced_at' => now(),
                    'gallery' => [$car['thumbnail_url']],
                    'description' => 'Vehículo revisado, historial de mantenimiento al día y entrega inmediata.',
                ]
            );
        }
    }
}
