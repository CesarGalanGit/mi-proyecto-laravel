<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween(18000, 70000);

        return [
            'order_id' => Order::factory(),
            'car_id' => Car::factory(),
            'car_name' => fake()->randomElement(['Audi A4', 'BMW Serie 3', 'Tesla Model 3']),
            'car_slug' => fake()->slug(),
            'unit_price' => $price,
            'quantity' => 1,
            'line_total' => $price,
            'car_snapshot' => [
                'year' => fake()->numberBetween(2018, 2025),
                'fuel_type' => fake()->randomElement(['Gasolina', 'Diésel', 'Híbrido', 'Eléctrico']),
                'transmission' => fake()->randomElement(['Automática', 'Manual']),
                'city' => fake()->city(),
            ],
        ];
    }
}
