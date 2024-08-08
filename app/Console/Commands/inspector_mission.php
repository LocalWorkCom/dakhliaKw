<?php

namespace App\Console\Commands;

use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\WorkingTime;
use App\Models\WorkingTree;
use App\Models\WorkingTreeTime;
use DateTime;
use Illuminate\Console\Command;

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
        // 'ids_group_point',
        // 'ids_instant_mission',

        // 'vacation_id',


        $start_day_date = date('Y-m-01');
        // $date = new DateTime('last day of previous month');
        // $last_day_previous_month = $date->format('Y-m-d');
        $num_days = date('t');


        // $date = new DateTime('last day of this month');
        // $last_day_current_month = $date->format('Y-m-d');




        $Inspectors = Inspector::pluck('id')->toArray();
        $count = 0;
        $is_repeat = 1;
        foreach ($Inspectors as $Inspector) {
            $date = $start_day_date; // Example date
            $GroupTeam = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$Inspector])->first();
            $WorkingTree = WorkingTree::find($GroupTeam->working_tree_id);

            if ($is_repeat) {
                $update_workingdays =  $WorkingTree->working_days_num;
                $update_holidays = $WorkingTree->holiday_days_num;
                if ($count + $WorkingTree->working_days_num > $num_days) {
                    $update_workingdays = $count - $WorkingTree->working_days_num;
                    $count = $update_workingdays;
                } else {
                    $count += $WorkingTree->working_days_num;
                }
                if ($count + $WorkingTree->holiday_days_num > $num_days) {
                    $update_holidays = $count - $WorkingTree->holiday_days_num;
                } else {
                    $count += $WorkingTree->holiday_days_num;
    
                }
                for ($i = 1; $i <= $update_workingdays; $i++) {
                    $WorkingTreeTime = WorkingTreeTime::where('working_tree_id', $WorkingTree->id)->where('day_num', $i)->first();

                    $date = date('Y-m-d', strtotime($date . ' +1 day'));


                    $inpectorMission = new InspectorMission();
                    $inpectorMission->inspector_id = $Inspector;
                    $inpectorMission->group_id = $GroupTeam->group_id;
                    $inpectorMission->group_team_id = $GroupTeam->id;
                    $inpectorMission->working_tree_id = $GroupTeam->working_tree_id;
                    $inpectorMission->working_time_id = $WorkingTreeTime->working_time_id;
                    $inpectorMission->date = $date;
                    $inpectorMission->save();
                }
                for ($i = 1; $i <= $update_holidays; $i++) {
                    $WorkingTreeTime = WorkingTreeTime::where('working_tree_id', $WorkingTree->id)->where('day_num', $i)->first();


                    $date = date('Y-m-d', strtotime($date . ' +1 day'));

                    $inpectorMission = new InspectorMission();
                    $inpectorMission->inspector_id = $Inspector;
                    $inpectorMission->group_id = $GroupTeam->group_id;
                    $inpectorMission->group_team_id = $GroupTeam->id;
                    $inpectorMission->working_tree_id = $GroupTeam->working_tree_id;
                    $inpectorMission->working_time_id = $WorkingTreeTime->working_time_id;
                    $inpectorMission->date = $date;
                    $inpectorMission->day_off = 1;
                    $inpectorMission->save();
                    if ($count >= $num_days) {
                        $is_repeat = 0;
                    } else {
                        $is_repeat = 1;
                    }
                }
            }
        }
    }
}
