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
use Illuminate\Support\Collection;

class governmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Define dates
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();

        // Get all sectors
        $allSectors = Sector::pluck('id')->toArray();

        foreach ($allSectors as $sector) {
            // Get all points available for this sector
            $allAvailablePoints = Grouppoint::where('sector_id', $sector)
                ->select('government_id', 'id', 'points_ids')
                ->get();

            // Get all groups and the number of points for each group in the sector
            $allGroupsForSector = Groups::where('sector_id', $sector)
                ->select('id', 'points_inspector')
                ->get();

            // Mapping to store government_id for each point
            $groupGovernmentIds = [];
            foreach ($allAvailablePoints as $point) {
                $groupGovernmentIds[$point->id] = $point->government_id;
            }

            foreach ($allGroupsForSector as $group) {
                // Get all teams of this group and their history
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                // Initialize variables
                $teamPointsYesterday = [];
                $teamPointsToday = [];
                $dayOffTeams = [];

                foreach ($groupTeams as $groupTeam) {
                    // Store yesterday's points
                    $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ? $groupTeam->ids_group_point : [];

                    // Points per team
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

                    // Filter available points based on the government_id of Grouppoint
                    $availablePoints = $allAvailablePoints->pluck('id')->toArray();
                    $filteredAvailablePoints = array_filter($availablePoints, function ($pointId) use ($groupGovernmentIds) {
                        return isset($groupGovernmentIds[$pointId]);
                    });

                    $availablePoints = $filteredAvailablePoints;

                    // Remove points assigned yesterday
                    $availablePoints = array_diff($availablePoints, $teamPointsYesterday[$groupTeam->group_team_id] ?? []);

                    if (!empty($availablePoints)) {
                        // Ensure the required number of points is selected
                        $possibleNewValues = array_splice($availablePoints, 0, $pointPerTeam);

                        // Ensure all new points are from the same government
                        $pointGovernmentIds = array_map(function ($pointId) use ($groupGovernmentIds) {
                            return $groupGovernmentIds[$pointId] ?? null;
                        }, $possibleNewValues);

                        if (count(array_unique($pointGovernmentIds)) > 1) {
                            // Points are from different governments, reshuffle and try again
                            $possibleNewValues = array_slice($availablePoints, 0, $pointPerTeam);
                        }
                        // Assign new values to the team
                        $teamPointsToday[$groupTeam->group_team_id] = $possibleNewValues;

                        // Update missions with new points
                        $updatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->where('day_off', 0)
                            ->pluck('id')
                            ->toArray();


                        foreach ($updatedMissions as $updatedMission) {
                            $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
                            if ($updated) {
                                // Update the ids_group_point field
                                $updated->ids_group_point = array_map('strval', $possibleNewValues);
                                $updated->save();

                                // Call notification function to notify the inspector with today's points
                            }
                        }

                        // Remove the assigned points from available points
                        $allAvailablePoints = $allAvailablePoints->whereNotIn('id', $possibleNewValues);
                    } else {
                        // Clear ids_group_point field as no points are available
                        $updatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->pluck('id')
                            ->toArray();

                        foreach ($updatedMissions as $updatedMission) {
                            $updated = InspectorMission::find($updatedMission);
                            if ($updated) {
                                $updated->ids_group_point = [];
                                $updated->save();
                            }
                        }
                    }
                }

                // Switch points if the same points were assigned yesterday
                foreach ($teamPointsYesterday as $teamId => $points) {
                    if (in_array($points, $teamPointsYesterday, true)) {
                        // Find a different team with matching points
                        foreach ($teamPointsToday as $otherTeamId => $otherPoints) {
                            if ($teamId !== $otherTeamId && $points === $teamPointsYesterday[$otherTeamId]) {
                                // Swap points
                                InspectorMission::where('group_team_id', $teamId)
                                    ->where('date', $today)
                                    ->update(['ids_group_point' => array_map('strval', $otherPoints)]);

                                InspectorMission::where('group_team_id', $otherTeamId)
                                    ->where('date', $today)
                                    ->update(['ids_group_point' => array_map('strval', $points)]);

                                // Update the team points
                                $teamPointsToday[$teamId] = $otherPoints;
                                $teamPointsToday[$otherTeamId] = $points;

                                break;
                            }
                        }
                    }
                }

                foreach ($dayOffTeams as $dayOffTeam) {
                    $updated = InspectorMission::find($dayOffTeam);
                    if ($updated) {
                        $updated->ids_group_point = [];
                        $updated->save();
                    }
                }
            }
        }
    }
    public function indexd()
    {

        // Get variable times
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();

        // Get all sectors
        $allSectors = Sector::pluck('id')->toArray();

        foreach ($allSectors as $sector) {
            // Get all points available for this sector
            $allAvailablePoints = Grouppoint::where('sector_id', $sector)
                ->select('government_id', 'id', 'points_ids')
                ->get();

            // Get all groups and the number of points for each group in the sector
            $allGroupsForSector = Groups::where('sector_id', $sector)
                ->select('id', 'points_inspector')
                ->get();

            // Mapping to store government_id for each point
            $groupGovernmentIds = [];
            foreach ($allAvailablePoints as $point) {
                $groupGovernmentIds[$point->id] = $point->government_id;
            }

            foreach ($allGroupsForSector as $group) {
                // Get all teams of this group and their history
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                // Initialize variables for new history and vacation of teams
                $usedValues = [];
                $dayOffTeams = [];
                $yesterdayPoints = [];

                foreach ($groupTeams as $groupTeam) {
                    // Store yesterday's points
                    $yesterdayPoints[$groupTeam->group_team_id] = $groupTeam->ids_group_point ? $groupTeam->ids_group_point : [];

                    // Points per team
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

                    // Filter available points based on the government_id of Grouppoint
                    $availablePoints = $allAvailablePoints->pluck('id')->toArray();
                    $filteredAvailablePoints = array_filter($availablePoints, function ($pointId) use ($groupGovernmentIds) {
                        return isset($groupGovernmentIds[$pointId]);
                    });

                    $availablePoints = $filteredAvailablePoints;

                    // Remove points assigned yesterday
                    $availablePoints = array_diff($availablePoints, $yesterdayPoints[$groupTeam->group_team_id] ?? []);

                    if (!empty($availablePoints)) {
                        // Merge and get points that were not taken yesterday
                        $allValues = array_merge($availablePoints, $groupTeam->ids_group_point ? $groupTeam->ids_group_point : []);
                        $allValues = array_unique($allValues);
                        shuffle($allValues);

                        // Determine possible new points
                        $possibleNewValues = array_diff($allValues, $usedValues, $yesterdayPoints[$groupTeam->group_team_id] ?? []);

                        // Ensure the required number of points is selected
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

                        // Ensure all new points are from the same government
                        $pointGovernmentIds = array_map(function ($pointId) use ($groupGovernmentIds) {
                            return $groupGovernmentIds[$pointId] ?? null;
                        }, $newValues);

                        if (count(array_unique($pointGovernmentIds)) > 1) {
                            // Points are from different governments, reshuffle and try again
                            $newValues = array_slice($availablePoints, 0, $requiredValuesCount);
                        }

                        // Assign new values to the team
                        $pointTeam = $newValues;
                        $usedValues = array_merge($usedValues, $newValues);

                        $updatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->where('day_off', 0)
                            ->pluck('id')
                            ->toArray();

                        foreach ($updatedMissions as $updatedMission) {
                            $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
                            if ($updated) {
                                // Update the ids_group_point field
                                $updated->ids_group_point = array_map('strval', $pointTeam);
                                $updated->save();

                                // Call notification function to notify the inspector with today's points
                            }
                        }

                        // Remove the assigned points from available points
                        $allAvailablePoints = $allAvailablePoints->whereNotIn('id', $pointTeam);
                    } else {
                        $updatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->pluck('id')
                            ->toArray();

                        foreach ($updatedMissions as $updatedMission) {
                            $updated = InspectorMission::find($updatedMission);
                            if ($updated) {
                                // Clear ids_group_point field as no points are available
                                $updated->ids_group_point = [];
                                $updated->save();
                            }
                        }
                    }
                }

                foreach ($dayOffTeams as $dayOffTeam) {
                    $updated = InspectorMission::find($dayOffTeam);
                    if ($updated) {
                        // Ensure ids_group_point is not updated for day-off teams
                        $updated->ids_group_point = [];
                        $updated->save();
                    }
                }
            }
        }
    }

    public function test()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        while ($startOfMonth->lte($endOfMonth)) {
            // Define dates for this iteration
            $today = $startOfMonth->toDateString();
            $yesterday = $startOfMonth->copy()->subDay()->toDateString();

            // Debugging information
            // $this->info("Processing Date - Today: $today, Yesterday: $yesterday");

            // Run your function for these dates
            $this->processDate($yesterday, $today);

            // Move to the next day
            $startOfMonth->addDay();
        }
    }

    private function processDate($yesterday, $today)
    {
        // Define dates
        // $yesterday and $today are passed as parameters
    
        // Get all sectors
        $allSectors = Sector::pluck('id')->toArray();
    
        foreach ($allSectors as $sector) {
            // Get all points available for this sector
            $allAvailablePoints = Grouppoint::where('sector_id', $sector)
                ->select('government_id', 'id', 'points_ids')
                ->get();
    
            // Get all groups and the number of points for each group in the sector
            $allGroupsForSector = Groups::where('sector_id', $sector)
                ->select('id', 'points_inspector')
                ->get();
    
            // Mapping to store government_id for each point
            $groupGovernmentIds = [];
            foreach ($allAvailablePoints as $point) {
                $groupGovernmentIds[$point->id] = $point->government_id;
            }
    
            foreach ($allGroupsForSector as $group) {
                // Get all teams of this group and their history for yesterday
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();
                  
                // Check if the groupTeams for yesterday is empty
                if ($groupTeams->isEmpty()) {
                  
                    // If empty, get the teams for today and initialize ids_group_point to an empty array
                    $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $today)
                    ->distinct('group_team_id')
                    ->get();
                    // dd($today,$groupTeams,$groupTeams->isEmpty());
                    // continue;
                }
             
    
                // Initialize variables
                $teamPointsYesterday = [];
                $teamPointsToday = [];
                $dayOffTeams = [];
    
                foreach ($groupTeams as $groupTeam) {
                    // Store yesterday's points
                    $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ? $groupTeam->ids_group_point : [];
    
                    // Points per team
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
    
                    // Filter available points based on the government_id of Grouppoint
                    $availablePoints = $allAvailablePoints->pluck('id')->toArray();
                    $filteredAvailablePoints = array_filter($availablePoints, function ($pointId) use ($groupGovernmentIds) {
                        return isset($groupGovernmentIds[$pointId]);
                    });
    
                    $availablePoints = $filteredAvailablePoints;
    
                    // Remove points assigned yesterday
                    $availablePoints = array_diff($availablePoints, $teamPointsYesterday[$groupTeam->group_team_id] ?? []);
    
                    if (!empty($availablePoints)) {
                        // Ensure the required number of points is selected
                        $possibleNewValues = array_splice($availablePoints, 0, $pointPerTeam);
    
                        // Ensure all new points are from the same government
                        $pointGovernmentIds = array_map(function ($pointId) use ($groupGovernmentIds) {
                            return $groupGovernmentIds[$pointId] ?? null;
                        }, $possibleNewValues);
    
                        if (count(array_unique($pointGovernmentIds)) > 1) {
                            // Points are from different governments, reshuffle and try again
                            $possibleNewValues = array_slice($availablePoints, 0, $pointPerTeam);
                        }
    
                        // Assign new values to the team
                        $teamPointsToday[$groupTeam->group_team_id] = $possibleNewValues;
    
                        // Update missions with new points
                        $updatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->where('day_off', 0)
                            ->pluck('id')
                            ->toArray();
    
                        foreach ($updatedMissions as $updatedMission) {
                            $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
                            if ($updated) {
                                // Update the ids_group_point field
                                $updated->ids_group_point = array_map('strval', $possibleNewValues);
                                $updated->save();
    
                                // Call notification function to notify the inspector with today's points
                            }
                        }
    
                        // Remove the assigned points from available points
                        $allAvailablePoints = $allAvailablePoints->whereNotIn('id', $possibleNewValues);
                    } else {
                        // Clear ids_group_point field as no points are available
                        $updatedMissions = InspectorMission::where('group_id', $group->id)
                            ->where('group_team_id', $groupTeam->group_team_id)
                            ->where('date', $today)
                            ->pluck('id')
                            ->toArray();
    
                        foreach ($updatedMissions as $updatedMission) {
                            $updated = InspectorMission::find($updatedMission);
                            if ($updated) {
                                $updated->ids_group_point = [];
                                $updated->save();
                            }
                        }
                    }
                }
    
                // Switch points if the same points were assigned yesterday
                foreach ($teamPointsYesterday as $teamId => $points) {
                    if (in_array($points, $teamPointsYesterday, true)) {
                        // Find a different team with matching points
                        foreach ($teamPointsToday as $otherTeamId => $otherPoints) {
                            if ($teamId !== $otherTeamId && $points === $teamPointsYesterday[$otherTeamId]) {
                                // Swap points
                                InspectorMission::where('group_team_id', $teamId)
                                    ->where('date', $today)
                                    ->update(['ids_group_point' => array_map('strval', $otherPoints)]);
    
                                InspectorMission::where('group_team_id', $otherTeamId)
                                    ->where('date', $today)
                                    ->update(['ids_group_point' => array_map('strval', $points)]);
    
                                // Update the team points
                                $teamPointsToday[$teamId] = $otherPoints;
                                $teamPointsToday[$otherTeamId] = $points;
    
                                break;
                            }
                        }
                    }
                }
    
                foreach ($dayOffTeams as $dayOffTeam) {
                    $updated = InspectorMission::find($dayOffTeam);
                    if ($updated) {
                        $updated->ids_group_point = [];
                        $updated->save();
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
    { {
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
    public function destroy(string $id) {}

    public function lastfuctionbeforlastupdate()
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
                            $upated = InspectorMission::where('id', $upatedMission)->where('vacation_id', null)->first();
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
