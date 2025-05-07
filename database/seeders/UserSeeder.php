<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Example password
            'role' => 'admin',
        ]);

        // Create 10 employers
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->company,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role' => 'employer',
            ]);
        }

        // Create 20 candidates
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role' => 'candidate',
            ]);
        }
    }
}
