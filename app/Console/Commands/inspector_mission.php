<?php

namespace App\Console\Commands;

use App\Models\EmployeeVacation;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\WorkingTime;
use App\Models\WorkingTree;
use App\Models\WorkingTreeTime;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class inspector_mission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:inspector_mission';

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
        // Set the start date to the 1st of the current month

        $start_day_date = date('Y-m-01');
        $num_days = date('t', strtotime($start_day_date)); // Get the number of days in the month
        $Inspectors = Inspector::where('flag', 0)->pluck('id')->toArray(); // get inspectors ids
        // to get inspectors ids
        foreach ($Inspectors as $Inspector) {
            $vacation_days = 0;
            $date = $start_day_date; // Start from the 1st of the month
            $GroupTeam = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$Inspector])->first(); //to get team for inspector
            // $temp_group_team = $GroupTeam->id;
            // if team exist
            if ($GroupTeam) {
                $ids_inspector = $GroupTeam->inspector_ids;
                $ids_inspector_arr = explode(",", $ids_inspector);
                sort($ids_inspector_arr);
                $WorkingTree = WorkingTree::find($GroupTeam->working_tree_id);
                if (!$WorkingTree || !$GroupTeam) {
                    Log::warning("Inspector ID $Inspector does not have a valid working tree or group team.");
                    continue;
                }
                //   to sum num of working day and holiday
                $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;
                // for loob by num of day's monthly
                $day_of_month_val = $GroupTeam->last_day;

                for ($day_of_month = $day_of_month_val; $day_of_month <= $num_days; $day_of_month++) {
                    // check day off or not

                    $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
                    // $is_day_off =  $WorkingTree->is_holiday;
                    // dd($is_day_off);
                    // if not  day off get working tree

                    $WorkingTreeTime =
                        WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                        ->where('day_num', $day_in_cycle)
                        ->first();

                    // if ($day_in_cycle == 4) {

                    // }


                    $user_id  = Inspector::find($Inspector)->user_id;
                    if ($vacation_days == 0) {

                        $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('start_date', '=',  $date)->first(); //1/9/2024
                        if ($EmployeeVacation) {
                            $vacation_days = $EmployeeVacation->days_number; //3
                        }
                    }



                    // insert data for monthly
                    $inspectorMission = new InspectorMission();
                    $inspectorMission->inspector_id = $Inspector;
                    $inspectorMission->group_id = $GroupTeam->group_id;
                    $inspectorMission->group_team_id = $GroupTeam->id;
                    $inspectorMission->working_tree_id = $GroupTeam->working_tree_id;
                    $inspectorMission->working_time_id = $WorkingTreeTime->working_time_id ? $WorkingTreeTime->working_time_id : null;
                    $inspectorMission->date = $date;
                    $inspectorMission->day_number = $day_in_cycle;

                    if ($vacation_days != 0) {
                        $inspectorMission->vacation_id = $EmployeeVacation->id;
                    }
                    $inspectorMission->day_off =  $WorkingTreeTime->working_time_id ? 0 : 1;
                    $inspectorMission->save();
                    if ($vacation_days != 0) {

                        $vacation_days--;
                    }

                    // Move to the next day
                    $date = date('Y-m-d', strtotime($date . ' +1 day'));
                }
                // if ($GroupTeam->last_day == 3) {

                //     dd($day_of_month);
                // }
                if ($GroupTeam->last_day > 1) {
                    $firstLoop = $day_of_month - $GroupTeam->last_day;
                    $day_in_cycle++;
                    for ($i = $firstLoop; $i < $num_days; $i++) {


                        // $is_day_off =  $WorkingTree->is_holiday;
                        // if not  day off get working tree

                        $WorkingTreeTime =
                            WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                            ->where('day_num', $day_in_cycle)
                            ->first();
                        if (!$WorkingTreeTime) {
                            $day_in_cycle = 1;
                            $WorkingTreeTime =
                                WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                                ->where('day_num', $day_in_cycle)
                                ->first();
                        }

                        $user_id  = Inspector::find($Inspector)->user_id;
                        if ($vacation_days == 0) {

                            $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('start_date', '=',  $date)->first(); //1/9/2024
                            if ($EmployeeVacation) {
                                $vacation_days = $EmployeeVacation->days_number; //3
                            }
                        }


                        // insert data for monthly
                        $inspectorMission = new InspectorMission();
                        $inspectorMission->inspector_id = $Inspector;
                        $inspectorMission->group_id = $GroupTeam->group_id;
                        $inspectorMission->group_team_id = $GroupTeam->id;
                        $inspectorMission->working_tree_id = $GroupTeam->working_tree_id;
                        $inspectorMission->working_time_id = $WorkingTreeTime->working_time_id ? $WorkingTreeTime->working_time_id : null;
                        $inspectorMission->date = $date;
                        $inspectorMission->day_number = $day_in_cycle;
                        if ($vacation_days != 0) {


                            $inspectorMission->vacation_id = $EmployeeVacation->id;
                        }
                        $inspectorMission->day_off =  $WorkingTreeTime->working_time_id ? 0 : 1;
                        $inspectorMission->save();
                        if ($vacation_days != 0) {

                            $vacation_days--;
                        }

                        // Move to the next day
                        $date = date('Y-m-d', strtotime($date . ' +1 day'));
                        if ($day_in_cycle > $total_days_in_cycle) {

                            $day_in_cycle = ($i - 1) % $total_days_in_cycle + 1;
                        } else {
                            $day_in_cycle++;
                        }
                    }
                }

                if ($ids_inspector_arr[sizeof($ids_inspector_arr) - 1] == $Inspector) {

                    $GroupTeam->last_day = $day_in_cycle;
                    $GroupTeam->save();
                }
            }
        }
    }
}
