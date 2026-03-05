<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = config('admin.email');

        if (!$adminEmail) {
            $this->command->warn('ADMIN_EMAIL is not configured. Skipping admin user creation.');
            return;
        }

        // Crear usuario admin si no existe
        $user = User::where('email', $adminEmail)->first();

        if (!$user) {
            User::create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]);
            $this->command->info("Admin user created with email: {$adminEmail}");
        } else {
            $this->command->comment("Admin user already exists with email: {$adminEmail}");
        }
    }
}
