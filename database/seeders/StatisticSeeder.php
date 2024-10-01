<?php

namespace Database\Seeders;

use App\Models\Statistic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $Statistics = [
            ['name' => 'الموظفين'],
            ['name' => 'المستخدمين'],
            ['name' => 'المجموعات'],
            ['name' => 'الادارات'],
            ['name' => 'الاجازات'],
            ['name' => 'اوامر خدمة'],
            ['name' => 'الصادر'],
            ['name' => 'الوارد'],
        ];
        //
        foreach ($Statistics as $gov) {
            Statistic::create($gov);
        }
    }
}
