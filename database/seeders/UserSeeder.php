<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Sample Staff',
                'password' => 'password', // hashed by the model cast
                'email_verified_at' => now(),
            ],
        );
    }
}
