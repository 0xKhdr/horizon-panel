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
        if (!User::query()->whereEmail($email = 'admin@horizon.local')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => $email,
            ]);
        }
    }
}
