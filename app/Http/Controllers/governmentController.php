<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
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
        $yesterday = Carbon::yesterday()->toDateString();
        // Fetch results with group_id, group_team_id, inspector_id, grouped_ids_group_point, num_points
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
            foreach ($governmentIds as $index => $governmentId) {
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
