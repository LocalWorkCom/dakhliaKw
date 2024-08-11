<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class governmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = InspectorMission::select(
            'inspector_mission.group_id',
            'inspector_mission.group_team_id',
            'inspector_mission.inspector_id',
            DB::raw('GROUP_CONCAT(inspector_mission.ids_group_point) as grouped_ids_group_point'),
            DB::raw('COALESCE(groups.points_inspector, 0) as num_points')
        )
        ->join('groups', 'inspector_mission.group_id', '=', 'groups.id')
        ->with(['inspector', 'group', 'groupTeam']) // Eager load the relationships
        ->groupBy('inspector_mission.group_id', 'inspector_mission.group_team_id', 'inspector_mission.inspector_id', 'groups.points_inspector')
        ->get();
        
        $availableGroupPoints = [];
        
        foreach ($results as $result) {
            $governmentId = $result->group->government_id;
        
            // Get all points assigned to teams within the current group
            $assignedPoints = DB::table('inspector_mission')
                ->where('group_id', $result->group_id)
                ->pluck('ids_group_point')
                ->flatMap(fn($json) => json_decode($json, true))
                ->toArray();
        
            // Fetch all group point IDs for the group, including their IDs, and exclude assigned points
            $groupPoints = DB::table('group_points')
                ->where('government_id', $governmentId)
                ->get(['id', 'points_ids'])
                ->flatMap(function ($pointGroup) use ($assignedPoints) {
                    $pointIds = json_decode($pointGroup->points_ids, true);
                    $availablePointIds = array_diff($pointIds, $assignedPoints);
                    return !empty($availablePointIds) ? [$pointGroup->id] : [];
                })
                ->toArray();
        
            // Flatten and shuffle available group points
            $allAvailablePoints = $groupPoints;
            shuffle($allAvailablePoints);
        
            // Calculate the number of points each team should get
           
          
            $numTeams = DB::table('inspector_mission')
                ->where('group_id', $result->group_id)
                ->distinct('group_team_id')
                ->count();
                
            $pointsPerTeam = $result->num_points;
            
            $remainingPoints = $pointsPerTeam % $numTeams;
        
            // Get team IDs
            $teamIds = DB::table('inspector_mission')
                ->where('group_id', $result->group_id)
                ->distinct('group_team_id')
                ->pluck('group_team_id')
                ->toArray();
        
            // Distribute points to teams
            $teamPoints = [];
            foreach ($teamIds as $index => $teamId) {
                $pointsToAssign = array_splice($allAvailablePoints, 0, $pointsPerTeam + ($remainingPoints > 0 ? 1 : 0));
                $teamPoints[$teamId] = $pointsToAssign;
                $remainingPoints--;
            }
        
            // Store or process the distributed points
            $availableGroupPoints[$result->group_id] = $teamPoints;
        }
            // dd($availableGroupPoints);
            foreach ($availableGroupPoints as $groupId => $teamsPoints) {
                foreach ($teamsPoints as $teamId => $points) {
                    // Convert points to JSON format for storage
                    $pointsJson = json_encode($points);
            
                    // Update the inspector_mission table
                    DB::table('inspector_mission')
                        ->where('group_id', $groupId)
                        ->where('group_team_id', $teamId)
                        ->update(['ids_group_point' => $pointsJson]);
                }
            }
        return view('grouppoints.create1', [
            'results' => $results,
            'availablePoints' => $availableGroupPoints
        ]);
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
        //
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
        //
    }
}
