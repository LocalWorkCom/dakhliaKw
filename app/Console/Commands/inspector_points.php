<?php

namespace App\Console\Commands;

use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Sector;
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
    /**
     * Display a listing of the resource.
     */

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
}
