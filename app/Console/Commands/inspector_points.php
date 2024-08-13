<?php

namespace App\Console\Commands;

use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\Inspector;
use App\Models\InspectorMission;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class inspector_points extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:inspector_points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get inspector team and group after this get the last points visited in previous day and select another points to visit that must be uniqe in same group';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = '2024-08-13';
        $today = '2024-08-14';
        $allGovernments = Government::pluck('id')->toArray();
        foreach ($allGovernments as $government) {
            $allAvailablePoints = Grouppoint::where('government_id', $government)->pluck('id')->toArray();
            $allGroupsForGovernment = Groups::where('government_id', $government)->select('id', 'points_inspector')->get();

            foreach ($allGroupsForGovernment as $group) {
                
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();
                    $usedValues = [];
                foreach ($groupTeams as $groupTeam) {
                    $pointPerTeam = $group->points_inspector;
                    if (!empty($allAvailablePoints)) {
                        $allValues = array_merge($allAvailablePoints, $groupTeam->ids_group_point ? $groupTeam->ids_group_point : []);
                        $allValues = array_unique($allValues);
                        shuffle($allValues);
                                // Determine possible new values that are not used and not the old values
                                $possibleNewValues = array_diff($allValues, $usedValues, $groupTeam->ids_group_point ? $groupTeam->ids_group_point : []);
                    
                                // If there are not enough values left, use whatever is available
                                $requiredValuesCount = count($groupTeam->ids_group_point ? $groupTeam->ids_group_point : []);
                                if (count($possibleNewValues) < $requiredValuesCount) {
                                    $newValues = array_slice($possibleNewValues, 0);
                                } else {
                                    $newValues = array_splice($possibleNewValues, 0, $requiredValuesCount);
                                }
                    
                                // If still not enough values, fill with any remaining values
                                if (count($newValues) < $requiredValuesCount) {
                                    $remainingValues = array_diff($allValues, $usedValues, $newValues);
                                    $newValues = array_merge($newValues, array_slice($remainingValues, 0, $requiredValuesCount - count($newValues)));
                                }
                    
                                // Assign new values to the team
                                $pointTeam = $newValues;
                                $usedValues = array_merge($usedValues, $newValues);

                            //dd($group->id . "  /   ".$groupTeam->group_team_id);
                            $upatedMissions = InspectorMission::where('group_id', $group->id)->where('group_team_id', $groupTeam->group_team_id)->where('date', $today)->where('day_off', 0)->pluck('id')->toArray();
                            
                            foreach ($upatedMissions as $upatedMission) {
                                $upated = InspectorMission::where('id',$upatedMission)->where('vacation_id',null)->first();
                                if ($upated) {
                                    
                                    // Update the ids_group_point field
                                    $upated->ids_group_point = array_map('strval', $pointTeam);

                                    // Save the updated record
                                    $upated->save();
                                }
                                $allAvailablePoints = array_diff($allAvailablePoints, $pointTeam);
                               // dd($allAvailablePoints);
                            }
                    } else {
                       // dd('k');
                        $upatedMissions = InspectorMission::where('group_id', $group->id)->where('group_team_id', $groupTeam->group_team_id)->where('date', $today)->pluck('id')->toArray();
                        foreach ($upatedMissions as $upatedMission) {
                            $upated = InspectorMission::find($upatedMission);
                            if ($upated) {
                                // Update the ids_group_point field
                                $upated->ids_group_point = [];

                                // Save the updated record
                                $upated->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
