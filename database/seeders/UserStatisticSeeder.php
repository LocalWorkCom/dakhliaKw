<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Statistic;
use App\Models\UserStatistic;
use Illuminate\Database\Seeder;

class UserStatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all statistics from the `statistics` table
        $statistics = Statistic::all();

        // Fetch all users from the `users` table
        $users = User::all();

        // Loop through each user and create a record for every statistic
        foreach ($users as $user) {
            foreach ($statistics as $statistic) {
                UserStatistic::create([
                    'user_id' => $user->id,
                    'statistic_id' => $statistic->id,
                    'checked' => true, // Set the `checked` column to true for all entries
                ]);
            }
        }
    }
}
