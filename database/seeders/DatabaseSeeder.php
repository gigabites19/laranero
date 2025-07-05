<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'account_hash' => Str::random(8),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);
    }
}
