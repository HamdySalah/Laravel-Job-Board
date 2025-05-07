<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Faker\Factory as Faker;

class ApplicationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $jobIds = Job::pluck('id')->toArray();
        $candidateIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 10; $i++) {
            Application::create([
                'job_id' => $faker->randomElement($jobIds),
                'candidate_id' => $faker->randomElement($candidateIds),
                'resume_path' => 'resumes/' . $faker->uuid . '.pdf',
                'message' => $faker->paragraph,
                'status' => $faker->randomElement(['pending', 'accepted', 'rejected']),
            ]);
        }
    }
}
