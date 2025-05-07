<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use Faker\Factory as Faker;

class JobSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $now = time();

        for ($i = 0; $i < 5; $i++) {
            $payload = json_encode([
                'job' => 'App\\Jobs\\SampleJob',
                'data' => [
                    'title' => $faker->jobTitle,
                    'company' => $faker->company,
                    'location' => $faker->city . ', ' . $faker->country,
                    'description' => $faker->sentence(12),
                ]
            ]);

            Job::create([
                'queue' => 'default',
                'payload' => $payload,
                'attempts' => rand(0, 2),
                'reserved_at' => $now,
                'available_at' => $now + rand(100, 500),
                'created_at' => $now,
            ]);
        }
    }
}
