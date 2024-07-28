<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VacationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'اعتيادي', 'active' => 1],
            ['name' => 'مرضي', 'active' => 1],
            ['name' => 'رسمية', 'active' => 1],
            ['name' => 'طارئة', 'active' => 1],
        ];

        // Insert data into the table
        foreach ($types as $type) {
            DB::table('vacation_types')->insert($type);
        }
    }
}
