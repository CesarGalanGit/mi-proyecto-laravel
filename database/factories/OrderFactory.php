<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->numberBetween(19000, 69000);
        $serviceFee = 500;

        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-'.strtoupper(fake()->bothify('######??')),
            'status' => fake()->randomElement(['pending', 'confirmed']),
            'subtotal' => $subtotal,
            'service_fee' => $serviceFee,
            'total' => $subtotal + $serviceFee,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('6########'),
            'notes' => fake()->optional()->sentence(),
            'placed_at' => now(),
        ];
    }
}
