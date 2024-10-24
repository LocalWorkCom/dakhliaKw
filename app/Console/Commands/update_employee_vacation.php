<?php

namespace App\Console\Commands;

use App\Models\EmployeeVacation;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class update_employee_vacation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update_employee_vacation';

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

            if ($EmployeeeVacation->status == 'Pending') {

                if ($EmployeeeVacation->start_date < $today && $expectedEndDate < $today) {


                    $EmployeeeVacation->status = 'Rejected';
                    $EmployeeeVacation->save();
                }
            }
        }
    }
}
