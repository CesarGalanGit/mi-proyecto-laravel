<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\CarReferralClick;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_tienda_catalogo_se_muestra_con_coches_disponibles(): void
    {
        Car::factory()->create([
            'brand' => 'BMW',
            'model' => 'Serie 1',
            'status' => 'available',
            'source_name' => 'Coches.net',
            'source_url' => 'https://www.coches.net/bmw-serie-1-123',
        ]);

        Car::factory()->create([
            'brand' => 'Audi',
            'model' => 'A3',
            'status' => 'reserved',
            'source_name' => 'Wallapop',
            'source_url' => 'https://es.wallapop.com/item/audi-a3-456',
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertOk();
        $response->assertSee('BMW Serie 1');
        $response->assertSee('Coches.net');
        $response->assertDontSee('Audi A3');
    }

    public function test_filtro_por_portal_de_origen_funciona_en_catalogo(): void
    {
        Car::factory()->create([
            'brand' => 'Volvo',
            'model' => 'XC90',
            'status' => 'available',
            'source_name' => 'Wallapop',
            'source_url' => 'https://es.wallapop.com/item/volvo-xc90-222',
        ]);

        Car::factory()->create([
            'brand' => 'Tesla',
            'model' => 'Model 3',
            'status' => 'available',
            'source_name' => 'Coches.net',
            'source_url' => 'https://www.coches.net/tesla-model-3-111',
        ]);

        $response = $this->get(route('shop.index', ['source' => 'Wallapop']));

        $response->assertOk();
        $response->assertSee('Volvo XC90');
        $response->assertDontSee('Tesla Model 3');
    }

    public function test_click_en_anuncio_redirige_al_portal_oficial(): void
    {
        $car = Car::factory()->create([
            'status' => 'available',
            'source_name' => 'Wallapop',
            'source_url' => 'https://es.wallapop.com/item/bmw-serie-3-999',
            'outbound_clicks' => 0,
        ]);

        $this->get(route('shop.outbound', $car))
            ->assertRedirect('https://es.wallapop.com/item/bmw-serie-3-999');

        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'outbound_clicks' => 1,
        ]);

        $this->assertDatabaseHas('car_referral_clicks', [
            'car_id' => $car->id,
            'source_name' => 'Wallapop',
            'destination_url' => 'https://es.wallapop.com/item/bmw-serie-3-999',
        ]);

        $this->assertSame(1, CarReferralClick::query()->where('car_id', $car->id)->count());
    }

    public function test_no_redirige_si_el_coche_no_tiene_url_oficial_o_no_esta_disponible(): void
    {
        $withoutUrl = Car::factory()->create([
            'status' => 'available',
            'source_url' => null,
        ]);

        $reserved = Car::factory()->create([
            'status' => 'reserved',
            'source_url' => 'https://www.coches.net/coche-reservado-123',
        ]);

        $this->get(route('shop.outbound', $withoutUrl))->assertNotFound();
        $this->get(route('shop.outbound', $reserved))->assertNotFound();
    }
}
