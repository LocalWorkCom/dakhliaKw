<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ViolationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('violation_type')->insert([
            ['name' => 'عسكرى',   'type_id' => '0' ,'created_by'=>1,'updated_by'=>1],
            ['name' => 'مدني', 'type_id' => '0' ,'created_by'=>1,'updated_by'=>1],

        ]);
    }
}
