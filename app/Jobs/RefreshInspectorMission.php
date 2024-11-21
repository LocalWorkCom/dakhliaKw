<?php

namespace App\Jobs;

use App\Models\EmployeeVacation;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\WorkingTree;
use App\Models\WorkingTreeTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class RefreshInspectorMission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $currentDate = Carbon::now();
        $lastMonthEnd = $currentDate->copy()->subMonth()->endOfMonth();
        $firstDayOfCurrentMonth = $currentDate->copy()->startOfMonth();
        $group_team_ids = [];

        $start_day_date = date('Y-m-d');
        $inspectorMissions = InspectorMission::whereBetween('date', [
            Carbon::now()->startOfMonth()->toDateString(),
            Carbon::now()->endOfMonth()->toDateString(),
        ])
            ->count();
        $new = false;
        $currentDate = Carbon::now();

        // Determine the total number of days in the current month
        $totalDaysInMonth = $currentDate->endOfMonth()->day;

        // $num_days = date('t', strtotime($start_day_date)); // Get the number of days in the month
        $num_days = $totalDaysInMonth - now()->day;

        $Inspectors = Inspector::where('flag', 0)->pluck('id')->toArray(); // get inspectors ids
        sort($Inspectors);
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
                    // Log::warning("Inspector ID $Inspector does not have a valid working tree or group team.");
                    continue;
                }

                //   to sum num of working day and holiday
                $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;
                // for loob by num of day's monthly




                if (!$inspectorMissions) {

                    $day_of_month_val = $GroupTeam->last_day;
                } else {

                    $data = InspectorMission::where('group_id', $GroupTeam->group_id)->where('group_team_id', $GroupTeam->id)
                        ->where('date', $date)->where('inspector_id', $Inspector)->first();
                    if ($data) {
                        $day_of_month_val = $data->day_number;
                    } else {
                        $new = true;
                        // dd($date,$Inspector,$GroupTeam->group_id,$GroupTeam->id,$data);
                        $day_of_month_val = 1;
                    }
                }

                if ($WorkingTree->changed || $GroupTeam->changed || !$inspectorMissions || $new) {
                    if ($WorkingTree->changed && sizeof($group_team_ids) == 0) {
                        $group_team_ids = GroupTeam::where('working_tree_id', $WorkingTree->id)->pluck('id')->toArray();
                    }

                    for ($day_of_month = $day_of_month_val; $day_of_month <= $num_days + 1; $day_of_month++) {
                        // check day off or not

                        $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
                        // $is_day_off =  $WorkingTree->is_holiday;


                        $WorkingTreeTime =
                            WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                            ->where('day_num', $day_in_cycle)
                            ->first();



                        $user_id  = Inspector::find($Inspector)->user_id;
                        if ($vacation_days == 0) {

                            $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('status', 'Approved')->where('start_date', '=',  $date)->first(); //1/9/2024
                            if ($EmployeeVacation) {
                                $vacation_days = $EmployeeVacation->days_number; //3
                            }
                        }
                        $working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;

                        $day_off = $working_time_id ? 0 : 1;
                        InspectorMission::where('group_id', $GroupTeam->group_id)->where('group_team_id', $GroupTeam->id)
                            ->where('date', $date)->where('inspector_id', $Inspector)
                            ->delete();
                        // insert data for monthly
                        $inspectorMission = new InspectorMission();
                        $inspectorMission->inspector_id = $Inspector;
                        $inspectorMission->group_id = $GroupTeam->group_id;
                        $inspectorMission->group_team_id = $GroupTeam->id;
                        $inspectorMission->working_tree_id = $GroupTeam->working_tree_id;
                        $inspectorMission->working_time_id = $working_time_id;
                        $inspectorMission->date = $date;
                        $inspectorMission->day_number = $day_in_cycle;

                        if ($vacation_days != 0) {
                            $inspectorMission->vacation_id = $EmployeeVacation->id;
                        }
                        $inspectorMission->day_off =  $day_off;
                        $inspectorMission->save();



                        if ($vacation_days != 0) {

                            $vacation_days--;
                        }

                        // Move to the next day
                        $date = date('Y-m-d', strtotime($date . ' +1 day'));
                    }
                    // if ($GroupTeam->last_day == 3) {

                    if ($ids_inspector_arr[sizeof($ids_inspector_arr) - 1] == $Inspector) {

                        $GroupTeam->last_day = $day_in_cycle;
                        $GroupTeam->changed = 0;
                        $GroupTeam->save();
                        $check = true;
                    }


                    // $this->updatePrevVacation($Inspector);
                }
                // dd($group_team_ids);
                if (($key = array_search($GroupTeam->id, $group_team_ids)) !== false && $ids_inspector_arr[sizeof($ids_inspector_arr) - 1] == $Inspector) {
                    unset($group_team_ids[$key]);
                }
                if (sizeof($group_team_ids) == 0) {

                    $WorkingTree->changed = 0;
                    $WorkingTree->save();
                }
            }
        }
    }

    function updatePrevVacation($Inspector)
    {
        $currentDate = Carbon::now();
        $user_id  = Inspector::find($Inspector)->user_id;


        $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)
            ->where('status', 'Approved')
            ->where('start_date', '<=', $currentDate) // Vacation started before or in current month
            ->where(function ($query) use ($currentDate) {
                $query->where('end_date', '>=', $currentDate)   // End date is in the future
                    ->orWhereNull('end_date');               // or no end date (ongoing)
            })
            ->first();

        if ($EmployeeVacation) {
            // Parse the start and end dates of the vacation
            $start_date = Carbon::parse($EmployeeVacation->start_date);
            $end_date = $EmployeeVacation->end_date ? Carbon::parse($EmployeeVacation->end_date) : Carbon::parse($currentDate);

            // Define the boundaries for last and current months
            $firstDayOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
            $lastDayOfLastMonth = Carbon::now()->subMonth()->endOfMonth();
            $firstDayOfCurrentMonth = Carbon::now()->startOfMonth();

            // Calculate vacation days in the last month if vacation started last month
            $vacationDaysLastMonth = 0;
            if ($start_date < $firstDayOfCurrentMonth) {
                $lastMonthStart = $start_date->greaterThanOrEqualTo($firstDayOfLastMonth) ? $start_date : $firstDayOfLastMonth;
                $lastMonthEnd = $end_date->lessThanOrEqualTo($lastDayOfLastMonth) ? $end_date : $lastDayOfLastMonth;

                $vacationDaysLastMonth = $lastMonthStart->diffInDays($lastMonthEnd) + 1;
            }

            // Calculate remaining vacation days for the current month
            $totalVacationDays = $EmployeeVacation->days_number;
            $remainingDays = $totalVacationDays - $vacationDaysLastMonth;

            // Check if remaining days extend into the current month
            $vacationDaysCurrentMonth = 0;
            // if ($remainingDays > 0) {
            $currentMonthEnd = $end_date->lessThanOrEqualTo($currentDate) ? $end_date : Carbon::parse($currentDate);
            $currentMonthStart = $firstDayOfCurrentMonth->greaterThan($start_date) ? $firstDayOfCurrentMonth : $start_date;
            $vacationDaysCurrentMonth = $currentMonthStart->diffInDays($currentMonthEnd) + 1;
            // }

            // Set vacation days based on calculations
            $vacation_days_last_month = $vacationDaysLastMonth;
            $vacation_days_current_month = $vacationDaysCurrentMonth;

            $vacation_days = $vacation_days_current_month;



            $inspectorMissions = InspectorMission::where('inspector_id', $Inspector)
                ->whereBetween('date', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateString(),
                ])
                ->orderBy('date')
                ->get();
            if ($inspectorMissions->count()) {
                for ($i = 0; $i < $vacation_days; $i++) {
                    $inspectorMissions[$i]->vacation_id = $EmployeeVacation->id;
                    $inspectorMissions[$i]->save();
                }
            }
        }
    }
}
