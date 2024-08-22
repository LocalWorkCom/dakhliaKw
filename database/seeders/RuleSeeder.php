<?php

namespace Database\Seeders;

use App\Models\Rule;
use App\Models\departements;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Rule::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $firstDepartment = departements::first();
        DB::table('rules')->insert([
            [
                'id' => 1,
                'name' => 'localworkadmin',
                'permission_ids' => '1,2,3,4,5,6,7,8,9,10,11,13,14,15,16,17,18,19,21,22,23,32,33,34,35,37,38,39,40,41,42,44,46,47,48,49,51,52,53,54,55,56,57,58,59,60,61,62,63,64,66,73,74,75,76,77,78,79,80,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118',
                'department_id' => $firstDepartment->id,
                'hidden' => 1
            ],
            [
                'id' => 2,
                'name' => 'superadmin',
                'permission_ids' => '1,2,4,9,10,11,13,15,16,17,18,19,21,22,23,32,33,34,35,37,38,39,40,41,42,44,46,47,48,49,51,52,53,54,55,56,57,58,59,60,61,62,63,64,66,73,74,75,76,77,78,79,80,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118',
                'department_id' => $firstDepartment->id,
                'hidden' => 0
            ],
            [
                'id' => 3,
                'name' => 'manager',
                'permission_ids' => '1,9,10,11,35,40,41,48,49,51,52,54,55,56,82,83,84,85,86,87,88,89,90,91,92,93,118',
                'department_id' => $firstDepartment->id,
                'hidden' => 0
            ],
            [
                'id' => 4,
                'name' => 'inspector',
                'permission_ids' => '35',
                'department_id' => $firstDepartment->id,
                'hidden' => 0
            ],

        ]);
    }
}
