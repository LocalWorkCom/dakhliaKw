<?php

namespace Database\Seeders;
use App\Models\Government;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class governatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governates = [
            ['name' => 'العاصمه' ],
            ['name' => 'حولى' ],
            ['name' => 'الأحمدى' ],
            ['name' => 'الجهراء' ],
            ['name' => 'الفروانية' ],
            ['name' => 'مبارك الكبير' ],
        ];
        //
        foreach ($governates as $gov) {
            Government::create($gov);
        }
        
    }
}
