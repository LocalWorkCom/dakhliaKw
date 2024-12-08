<?php

namespace Database\Seeders;

use App\Models\Rule;
use App\Models\departements;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class pointOptions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('point_options')->insert([
            [
                'id' => 1,
                'name' => 'الأسلحه'
            ],
            [
                'id' => 2,
                'name' => 'عدد اللاسلكيات'
            ],
            [
                'id' => 3,
                'name' => 'عدد الاليات'
            ],
            [
                'id' => 4,
                'name' => 'عدد الفاكسات'
            ],
            [
                'id' => 5,
                'name' => 'عدد الات التصوير'
            ],
            [
                'id' => 6,
                'name' => 'عدد الكومبيوتر'
            ],
            [
                'id' => 7,
                'name' => 'عدد السيارات المحجوزه'
            ],
            [
                'id' => 8,
                'name' => 'الموقوفين'
            ],
        ]);
    }
}
