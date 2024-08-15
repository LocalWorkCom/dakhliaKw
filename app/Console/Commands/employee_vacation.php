<?php

namespace App\Console\Commands;

use App\Models\EmployeeVacation;
use App\Models\Inspector;
use App\Models\InspectorMission;
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
                    $inspector = Inspector::where('user_id', $EmployeeeVacation->employee_id)->first();

                    if ($inspector) {
                        // Fetch InspectorMission records for the found inspector ID
                        $inspectorMission = InspectorMission::where('inspector_id', $inspector->id)
                            ->whereDate('date', '=', $today)
                            ->first();

                        // Update the InspectorMission record with the vacation ID
                        $inspectorMission->vacation_id = $EmployeeeVacation->id;
                        $inspectorMission->save();
                    }
                }
            }
        }
    }
}
