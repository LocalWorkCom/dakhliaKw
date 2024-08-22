<?php

namespace Database\Seeders;

use App\Models\departements;
use App\Models\Rule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $firstDepartment = departements::first();
        $rule= Rule::first();
        DB::table('users')->insert([
            [
             'name' => 'LocalWorkAdmin',  
             'email' => 'dev@localwork.com' ,
             'password' => Hash::make('pa@$$w0rd'),
             'military_number'=>"1111",
             'Civil_number'=>"1111",
             'file_number'=>"1111",
             'rule_id' => $rule->id,
             'department_id'=> $firstDepartment->id,
             'phone'=>'01114057863',
             'flag'=>'user',
             'country_code'=>'+20',
            ],

        ]);
    }
}
