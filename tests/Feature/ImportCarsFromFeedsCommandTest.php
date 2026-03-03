<?php

namespace Tests\Feature;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImportCarsFromFeedsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_importa_anuncios_reales_desde_un_conector_especifico(): void
    {
        Config::set('car_import.connectors', [
            'wallapop' => [
                'search_url' => 'https://example.test/wallapop-search',
            ],
            'cochesnet' => [
                'search_url' => null,
            ],
            'autoscout24' => [
                'search_url' => null,
            ],
            'milanuncios' => [
                'search_url' => null,
            ],
        ]);

        Http::fake([
            'https://example.test/wallapop-search' => Http::response(<<<'HTML'
                <html><body>
                    <a href="https://es.wallapop.com/item/bmw-x1-1001">BMW X1</a>
                </body></html>
            HTML),
            'https://es.wallapop.com/item/bmw-x1-1001' => Http::response(<<<'HTML'
                <html>
                    <head>
                        <meta property="og:title" content="BMW X1 2022" />
                        <meta property="og:description" content="Vehículo impecable con historial completo." />
                        <meta property="og:image" content="https://images.example.com/bmw-x1.jpg" />
                    </head>
                    <body>
                        <p>Precio final 33.990 €</p>
                        <p>28.000 km</p>
                        <p>Diésel</p>
                        <p>Automática</p>
                        <p>Negro</p>
                        <p>Madrid</p>
                    </body>
                </html>
            HTML),
        ]);

        $this->artisan('cars:import-feeds', [
            '--connector' => ['wallapop'],
            '--limit' => 10,
        ])
            ->expectsOutputToContain('Importación completada correctamente.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('cars', [
            'brand' => 'BMW',
            'model' => 'X1',
            'year' => 2022,
            'source_name' => 'Wallapop',
            'source_url' => 'https://es.wallapop.com/item/bmw-x1-1001',
            'price' => 33990.00,
        ]);

        /** @var Car $car */
        $car = Car::query()->firstOrFail();

        $this->assertSame('Diésel', $car->fuel_type);
        $this->assertSame('Automática', $car->transmission);
        $this->assertNotNull($car->last_synced_at);
    }

    public function test_dry_run_por_conector_no_escribe_en_base_de_datos(): void
    {
        Config::set('car_import.connectors', [
            'wallapop' => [
                'search_url' => null,
            ],
            'cochesnet' => [
                'search_url' => 'https://example.test/cochesnet-search',
            ],
            'autoscout24' => [
                'search_url' => null,
            ],
            'milanuncios' => [
                'search_url' => null,
            ],
        ]);

        Http::fake([
            'https://example.test/cochesnet-search' => Http::response(<<<'HTML'
                <html><body>
                    <a href="https://www.coches.net/audi-a3-2002">Audi A3</a>
                </body></html>
            HTML),
            'https://www.coches.net/audi-a3-2002' => Http::response(<<<'HTML'
                <html>
                    <head>
                        <meta property="og:title" content="Audi A3 2021" />
                        <meta property="og:image" content="https://images.example.com/audi-a3.jpg" />
                    </head>
                    <body>
                        <p>Precio 25.990 €</p>
                    </body>
                </html>
            HTML),
        ]);

        $this->artisan('cars:import-feeds', [
            '--connector' => ['cochesnet'],
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('Dry run completado. No se guardaron cambios.')
            ->assertExitCode(0);

        $this->assertDatabaseCount('cars', 0);
    }
}
