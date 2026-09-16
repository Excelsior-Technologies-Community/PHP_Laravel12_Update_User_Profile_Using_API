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
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'alex@example.com'],
            [
                'name' => 'Alex Johnson',
                'password' => \Illuminate\Support\Facades\Hash::make('Password@123'),
                'phone' => '+91 9876543210',
                'bio' => 'Full Stack Developer',
                'city' => 'Ahmedabad',
                'country' => 'India',
                'website' => 'https://example.com',
                'github_profile' => 'https://github.com/developer',
                'twitter_profile' => 'https://twitter.com/developer',
                'timezone' => 'Asia/Kolkata',
            ]
        );
    }
}
