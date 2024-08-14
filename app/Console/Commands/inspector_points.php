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
        //get vaiable times
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();
        //get all governments
        $allGovernments = Government::pluck('id')->toArray();
        
        foreach ($allGovernments as $government) { // fetch on all governments
            //get all points available for this government
            $allAvailablePoints = Grouppoint::where('government_id', $government)->pluck('id')->toArray();
            //get all groups and num of points for each group 
            $allGroupsForGovernment = Groups::where('government_id', $government)->select('id', 'points_inspector')->get();
        
            foreach ($allGroupsForGovernment as $group) { //fetch on all groups in same governmet
                //get all teams of this group and his history
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();
                // inisialize variables for new history and vacation of team
                $usedValues = [];
                $dayOffTeams = [];
        
                foreach ($groupTeams as $groupTeam) { // fetch on teams on same group
                    //points for each team that should take
                    $pointPerTeam = $group->points_inspector;
        
                    // Collect day-off teams
                    $teamsWithDayOff = InspectorMission::where('group_id', $group->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->where('date', $today)
                        ->where('day_off', 1)
                        ->pluck('id')
                        ->toArray();
                    
                    if (!empty($teamsWithDayOff)) {
                        $dayOffTeams = array_merge($dayOffTeams, $teamsWithDayOff);
                        continue;
                    }
        
                    if (!empty($allAvailablePoints)) {
                        //merging points and get the points that not takes yesterday
                        $allValues = array_merge($allAvailablePoints, $groupTeam->ids_group_point ? $groupTeam->ids_group_point : []);
                        $allValues = array_unique($allValues);
                        shuffle($allValues);
        
                        // Determine possible new points that are not used and not the old values
                        $possibleNewValues = array_diff($allValues, $usedValues, $groupTeam->ids_group_point ? $groupTeam->ids_group_point : []);
                    
                        // If there are not enough points left, use whatever is available
                        $requiredValuesCount = $pointPerTeam;
                        if (count($possibleNewValues) < $requiredValuesCount) {
                            $newValues = array_slice($possibleNewValues, 0);
                        } else {
                            $newValues = array_splice($possibleNewValues, 0, $requiredValuesCount);
                        }
                    
                        // If still not enough points, fill with any remaining values
                        if (count($newValues) < $requiredValuesCount) {
                            $remainingValues = array_diff($allValues, $usedValues, $newValues);
                            $newValues = array_merge($newValues, array_slice($remainingValues, 0, $requiredValuesCount - count($newValues)));
                        }
                    
                        // Assign new values to the team
                        $pointTeam = $newValues;
                        $usedValues = array_merge($usedValues, $newValues);
        
                        $upatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->where('day_off', 0)
                            ->pluck('id')
                            ->toArray();
                        
                        foreach ($upatedMissions as $upatedMission) {
                            $upated = InspectorMission::where('id', $upatedMission)->where('vacation_id', null)->first();
                            if ($upated) {
                                // Update the ids_group_point field
                                $upated->ids_group_point = array_map('strval', $pointTeam);
                                $upated->save();
                            }
                        }
        
                        // Remove the assigned points from available points
                        $allAvailablePoints = array_diff($allAvailablePoints, $pointTeam);
                    } else {
                        $upatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->pluck('id')
                            ->toArray();
                        
                        foreach ($upatedMissions as $upatedMission) {
                            $upated = InspectorMission::find($upatedMission);
                            if ($upated) {
                                // Update the ids_group_point field
                                $upated->ids_group_point = [];
                                $upated->save();
                            }
                        }
                    }
                }
                foreach ($dayOffTeams as $dayOffTeam) {
                    $upated = InspectorMission::find($dayOffTeam);
                    if ($upated) {
                        // Ensure ids_group_point is not updated for day off teams
                        $upated->ids_group_point = [];
                        $upated->save();
                    }
                }
            }
        }
    }
}
