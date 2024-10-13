<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ViolationTypeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'teest@example.com',
        // ]);

        $this->call(RuleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ViolationTypeSeeder::class);
        $this->call(StatisticSeeder::class);
        $this->call(UserStatisticSeeder::class);
    }
}
