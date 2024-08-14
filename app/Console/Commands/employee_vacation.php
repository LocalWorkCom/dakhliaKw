<?php

namespace App\Console\Commands;

use App\Models\EmployeeVacation;
use Illuminate\Console\Command;

class employee_vacation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:employee_vacation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = date('Y-m-d');

        $EmployeeVacations = EmployeeVacation::all();
        foreach ($EmployeeVacations as $EmployeeeVacation) {
            $expectedEndDate = ExpectedEndDate($EmployeeeVacation)[0];

            if ($EmployeeeVacation->status == 'Approved') {

                if ($EmployeeeVacation->start_date < $today && $expectedEndDate < $today && (!$EmployeeeVacation->end_date || $EmployeeeVacation->end_date > $today)) {
                    $EmployeeeVacation->is_exceeded = 1;
                    $EmployeeeVacation->save();
                }
            }
        }
    }
}
