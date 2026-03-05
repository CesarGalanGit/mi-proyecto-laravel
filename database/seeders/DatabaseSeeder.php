<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CarSeeder::class);

        // Crear usuario admin si no existe, evitando factories (Faker) en producción
        if (! User::where('email', 'test@example.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'test@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
        }
    }
}
