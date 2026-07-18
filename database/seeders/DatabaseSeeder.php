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
        // Admin account — change these credentials via .env (ADMIN_EMAIL / ADMIN_PASSWORD).
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@quickies.test')],
            [
                'name' => 'Quickies Admin',
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );
    }
}
