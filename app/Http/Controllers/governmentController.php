<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\instantmission;
use App\Models\Sector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class governmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {  
        //get vaiable times
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();
        //get all Sector
        $allsectors = Sector::pluck('id')->toArray();
        
        foreach ($allsectors as $sector) { // fetch on all sectors
            //get all points available for this government
            $allAvailablePoints = Grouppoint::where('sector_id', $sector)->pluck('id')->toArray();
            //get all groups and num of points for each group 
            $allGroupsForGovernment = Groups::where('sector_id', $sector)->select('id', 'points_inspector')->get();
        
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
                                
                                // call notification function to notify this inspector with his points for today
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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $records_inspectors = InspectorMission::select('inspector_id', 'ids_group_point', 'group_id', 'group_team_id')->get();

        // Initialize an array to store the results
        $allPoints = [];

        foreach ($records_inspectors as $record) {
            // Fetch the number of points required from the Groups table
            $pointsCountforTeam = Groups::where('id', $record->group_id)->value('points_inspector');
            $teamscoutForGroup = GroupTeam::where('group_id', $record->group_id)->get();
            if ($pointsCountforTeam) {
                // Fetch the limited number of Grouppoint records
                $points = Grouppoint::limit($pointsCountforTeam)->get();

                // Store the results in the array
                $allPoints[$record->group_id] = $points;
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        // Fetch results with group_id, num_points
        $results = InspectorMission::select(
            'inspector_mission.group_id',
            'inspector_mission.date',
            DB::raw('COALESCE(groups.points_inspector, 0) as num_points')
        )
            ->join('groups', 'inspector_mission.group_id', '=', 'groups.id')->whereDate('date', $yesterday)->distinct()
            ->with(['group', 'groupTeam'])
            ->groupBy('inspector_mission.group_id',  'groups.points_inspector', 'inspector_mission.date')
            ->get();
        $availableGroupPoints = []; // Variable for available points

        foreach ($results as $result) {
            // Get government_id of each group
            $governmentId  = $result->group->government_id;
            $governmentIds[] = $result->group->government_id;
            // Get team IDs
            $teamIds = DB::table('inspector_mission')
                ->where('group_id', $result->group_id)
                ->distinct('group_team_id')
                ->pluck('group_team_id')
                ->toArray();

            // This is the history of group points (returns an array of ids)
            $assignedPoints = DB::table('inspector_mission')
                ->whereIn('group_team_id', $teamIds)
                ->whereDate('date', $yesterday)
                ->pluck('ids_group_point')
                ->flatMap(fn($json) => json_decode($json, true))
                ->toArray();

            // Fetch all group point IDs for the group, excluding assigned points (returns an array of ids)
            $groupPoints = DB::table('group_points')
                ->where('government_id', $governmentId)
                ->get(['id', 'points_ids'])
                ->flatMap(function ($pointGroup) use ($assignedPoints) {
                    $pointIds = json_decode($pointGroup->points_ids, true);
                    $availablePointIds = array_diff($pointIds, $assignedPoints);
                    return !empty($availablePointIds) ? [$pointGroup->id] : [];
                })
                ->toArray();
            // Shuffle available group points
            shuffle($groupPoints);

            // Number of points each team should get
            $pointsPerTeam = $result->num_points;

            // Initialize arrays to track assignments
            $teamPoints = [];
            foreach ($governmentIds as $index => $government) {
              
                if ($index > 0 && $governmentId == $governmentIds[$index - 1]) {
                  
                    $usedPoints = !empty($pointsToAssign) ? $pointsToAssign : [];

                    // dd($usedPoints);
                    foreach ($teamIds as $teamId) {
                        // Ensure points are unique across teams
                        $pointsToAssign = array_splice($groupPoints, 0, $pointsPerTeam);

                        $pointsToAssign = array_diff($pointsToAssign, $usedPoints); // Remove already used points

                        $usedPoints = array_merge($usedPoints, $pointsToAssign); // Track the points that are used
                        $pointsToAssign = array_slice($pointsToAssign, 0, $pointsPerTeam); // Limit to the number of points needed
                        // Assign points to team or leave empty if no points available
                        $teamPoints[$teamId] = !empty($pointsToAssign) ? $pointsToAssign : [];
                    }
                } else {  
                    $groupPoints = DB::table('group_points')
                        ->where('government_id', $governmentId)
                        ->get(['id', 'points_ids'])
                        ->flatMap(function ($pointGroup) use ($assignedPoints) {
                            $pointIds = json_decode($pointGroup->points_ids, true);
                            $availablePointIds = array_diff($pointIds, $assignedPoints);
                            return !empty($availablePointIds) ? [$pointGroup->id] : [];
                        })
                        ->toArray();
                    shuffle($groupPoints);

                    $usedPoints = [];
                    foreach ($teamIds as $teamId) {
                        // Ensure points are unique across teams
                        $pointsToAssign = array_splice($groupPoints, 0, $pointsPerTeam);

                        $pointsToAssign = array_diff($pointsToAssign, $usedPoints); // Remove already used points
                        // dd()
                        $usedPoints = array_merge($usedPoints, $pointsToAssign); // Track the points that are used
                        $pointsToAssign = array_slice($pointsToAssign, 0, $pointsPerTeam); // Limit to the number of points needed
                        // Assign points to team or leave empty if no points available
                        $teamPoints[$teamId] = !empty($pointsToAssign) ? $pointsToAssign : [];

                        // If there are no points left, we break out of the loop
                        if (empty($groupPoints)) {
                            break;
                        }
                    }
                }
            }
            // Store or process the distributed points
            $availableGroupPoints[$result->group_id] = $teamPoints;
        }
        //  dd($availableGroupPoints);
        $today = Carbon::today()->toDateString();

        // Update records in inspector_mission table for the current day
        foreach ($availableGroupPoints as $groupId => $teams) {
            foreach ($teams as $teamId => $points) {
                $pointsJson = json_encode($points);

                // Perform the update
                $affectedRows = DB::table('inspector_mission')
                    ->where('group_id', $groupId)
                    ->where('group_team_id', $teamId)
                    ->whereDate('date', $today)
                    ->update(['ids_group_point' => $pointsJson]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       
    }

    public function lastfuctionbeforlastupdate(){
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
