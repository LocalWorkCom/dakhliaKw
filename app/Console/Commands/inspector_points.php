<?php

namespace App\Console\Commands;

use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Sector;
use Carbon\Carbon;
use App\Models\Point;
use DateTime;
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
     public function __construct()
    {
        parent::__construct();
    }
   
   

    public function handle()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        while ($startOfMonth->lte($endOfMonth)) {
            $today = $startOfMonth->toDateString();
            $yesterday = $startOfMonth->copy()->subDay()->toDateString();
            $this->teamOfGroup($yesterday, $today);
            $startOfMonth->addDay();
        }
    }

    public function todayIndex($today)
    {
        $daysOfWeek = [
            "السبت",
            "الأحد",
            "الاثنين",
            "الثلاثاء",
            "الأربعاء",
            "الخميس",
            "الجمعة",
        ];

        $todayDate = Carbon::parse($today);
        $dayWeek = $todayDate->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);

        return $index !== false ? $index : null;
    }

    // function addValueToval($array, $key, $value)
    // {
    //     if (array_key_exists($key, $array)) {
    //         if (is_array($array[$key])) {
    //             $array[$key][] = $value;
    //         } else {
    //             $array[$key] = [$array[$key], $value];
    //         }
    //     } else {
    //         $array[$key] = [$value];
    //     }

    //     return $array;
    // }

    public function getAvailablePoints($index, $sector, $group, $team, $teamTimePeriods, $historyOfTeam, $pointCount, $userToday = null)
    {
        $idsOfHistory = [];
        $idsOfTodayUsed = [];
        $validPoints = [];

        if ($historyOfTeam) {
            $idsOfHistory = Grouppoint::whereIn('id', $historyOfTeam)->pluck('points_ids')
                ->flatten()
                ->toArray();
        }
        if ($userToday) {
            $idsOfTodayUsed = Grouppoint::whereIn('id', $userToday)
                ->pluck('points_ids')
                ->flatten()
                ->toArray();
        }

        $allPoints = Point::with('pointDays')->where('sector_id', $sector);
        if (count($idsOfTodayUsed) > 0) {
            $allPoints->whereNotIn('id', $idsOfTodayUsed);
        }
        if (count($idsOfHistory) > 0) {
            $allPoints->whereNotIn('id', $idsOfHistory);
        }
        $availablePoints = $allPoints->get();

        $assignedPointsToday = [];

        foreach ($availablePoints as $available_point) {
            if ($available_point->work_type == 0) {
                $is_off = in_array($index, $available_point->days_work);
                if ($is_off) {
                    $pointId = '' . $available_point->id . '';
                    $id_groupoints = Grouppoint::whereJsonContains('points_ids', $pointId)
                        ->where('deleted', 0)
                        ->pluck('id', 'government_id')
                        ->toArray();

                    foreach ($id_groupoints as $government_id => $id) {
                        if (!in_array($id, $assignedPointsToday)) {
                            $validPoints[] = [
                                'id' => $id,
                                'government_id' => $government_id
                            ];
                            $assignedPointsToday[] = $id;
                        }
                    }
                }
            } else {
                $pointDay = $available_point->pointDays->where('name', $index)->first();

                if ($pointDay) {
                    $is_available = $this->isTimeAvailable($pointDay->from, $pointDay->to, $teamTimePeriods[0][0], $teamTimePeriods[0][1]);
                    if ($is_available) {
                        $pointId = '' . $available_point->id . '';
                        $id_groupoints = Grouppoint::whereJsonContains('points_ids', $pointId)
                            ->where('deleted', 0)
                            ->pluck('id', 'government_id')
                            ->toArray();
                        foreach ($id_groupoints as $government_id => $id) {
                            if (!in_array($id, $assignedPointsToday)) {
                                $validPoints[] = [
                                    'id' => $id,
                                    'government_id' => $government_id
                                ];
                                $assignedPointsToday[] = $id;
                            }
                        }
                    }
                }
            }
        }

        $teamPoints = $this->getItemsByGovernmentId($validPoints, $pointCount);
        $new = array_column($teamPoints, 'id');
        return $new;
    }

    function getItemsByGovernmentId($array, $numberOfItems)
    {
        $grouped = [];
        foreach ($array as $item) {
            $grouped[$item['government_id']][] = $item;
        }

        $result = [];

        foreach ($grouped as $items) {
            if (is_array($items)) {
                shuffle($items);

                if (count($items) >= $numberOfItems) {
                    $result = array_slice($items, 0, $numberOfItems);
                    break;
                }
            }
        }

        if (empty($result)) {
            $firstGroup = reset($grouped);
            if (is_array($firstGroup)) {
                shuffle($firstGroup);
                $result = $firstGroup;
            }
        }

        return $result;
    }

    // function formatTimeInArabic($time)
    // {
    //     $dateTime = new DateTime($time);
    //     $formattedTime = $dateTime->format('g:i');
    //     $suffix = $dateTime->format('a') === 'am' ? 'صباحا' : 'مساء';
    //     return $formattedTime . ' ' . $suffix;
    // }

    function isTimeAvailable($pointStart, $pointEnd, $teamStart, $teamEnd)
    {
        $pointStartTimestamp = strtotime($pointStart);
        $pointEndTimestamp = strtotime($pointEnd);
        $teamStartTimestamp = strtotime($teamStart);
        $teamEndTimestamp = strtotime($teamEnd);

        return $teamStartTimestamp <= $pointStartTimestamp && $teamStartTimestamp >= $pointEndTimestamp && $teamEndTimestamp >= $pointStartTimestamp;
    }

    public function teamOfGroup($yesterday, $today)
    {
        $todayIndex = $this->todayIndex($today);
        $allSectors = Sector::all();

        foreach ($allSectors as $sector) {
            $allGroups = Groups::where('sector_id', $sector->id)->get();

            foreach ($allGroups as $group) {
                $teams = GroupTeam::where('group_id', $group->id)->pluck('id')->toArray();
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->whereIn('group_team_id', $teams)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                if ($groupTeams->isEmpty()) {
                    $groupTeams = InspectorMission::where('group_id', $group->id)
                        ->whereIn('group_team_id', $teams)
                        ->select('group_team_id', 'ids_group_point')
                        ->whereDate('date', $today)
                        ->distinct('group_team_id')
                        ->get();
                }

                // Get count of points already assigned to each team
                $teamPointsCount = $groupTeams->mapWithKeys(function ($team) {
                    return [
                        $team->group_team_id => count($team->ids_group_point ?? [])
                    ];
                })->toArray();

                $dayOffTeams = [];
                $usedPointsToday = [];
                $groupTeams = $groupTeams->shuffle(); // Shuffle to ensure random distribution

                foreach ($groupTeams as $groupTeam) {
                    $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ?: [];
                    $pointPerTeam = $group->points_inspector;

                    $teamsWorkingTime = InspectorMission::with('workingTime')
                        ->where('group_id', $group->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 0)
                        ->distinct('group_team_id')
                        ->get();

                    $teamTimePeriods = $teamsWorkingTime->map(function ($mission) {
                        return [$mission->workingTime->start_time, $mission->workingTime->end_time];
                    })->toArray();

                    $teamsWithDayOff = InspectorMission::where('group_id', $group->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 1)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($teamsWithDayOff)) {
                        $dayOffTeams = array_merge($dayOffTeams, $teamsWithDayOff);
                        continue;
                    }

                    if (empty($teamsWithDayOff) && !empty($teamTimePeriods)) {
                        // Get available points excluding those used today
                        $pointOfTeam = $this->getAvailablePoints($todayIndex, $sector->id, $group->id, $groupTeam->group_team_id, $teamTimePeriods, $teamPointsYesterday[$groupTeam->group_team_id], $pointPerTeam, $usedPointsToday);
                    }
                    $usedPointsToday = array_merge($usedPointsToday, $pointOfTeam);

                    $updatedMissions = InspectorMission::where('group_id', $group->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 0)
                        ->pluck('id')
                        ->toArray();

                    foreach ($updatedMissions as $updatedMission) {
                        $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
                        if ($updated) {
                            // Ensure points distribution is fair
                            $updated->ids_group_point = array_map('strval', $pointOfTeam);
                            $updated->save();
                        }
                    }

                    // Update team points count
                    $teamPointsCount[$groupTeam->group_team_id] = ($teamPointsCount[$groupTeam->group_team_id] ?? 0) + count($pointOfTeam);

                    // Recheck to balance if needed
                    $this->balancePointsDistribution($teamPointsCount, $group->id, $today);
                }
            }
        }
    }


    private function balancePointsDistribution($teamPointsCount, $groupId, $date)
    {
        // Example logic for rebalancing
        $totalPoints = array_sum($teamPointsCount);
        $averagePoints = $totalPoints / count($teamPointsCount);

        foreach ($teamPointsCount as $teamId => $pointsCount) {
            if ($pointsCount < $averagePoints) {
                // Add more points to this team
                // Logic for adding points
            } else if ($pointsCount > $averagePoints) {
                // Remove excess points from this team
                // Logic for redistributing points
            }
        }
    }

    // public function handle()
    // {
    //     $startOfMonth = Carbon::now()->startOfMonth();
    //     $endOfMonth = Carbon::now()->endOfMonth();

    //     while ($startOfMonth->lte($endOfMonth)) {
    //         // Define dates for this iteration
    //         $today = $startOfMonth->toDateString();
    //         $yesterday = $startOfMonth->copy()->subDay()->toDateString();

    //         // Debugging information
    //         // $this->info("Processing Date - Today: $today, Yesterday: $yesterday");

    //         // Run your function for these dates
    //         $this->processDate($yesterday, $today);

    //         // Move to the next day
    //         $startOfMonth->addDay();
    //     }
    // }

    // private function processDate($yesterday, $today)
    // {
    //     // Define dates
    //     // $yesterday and $today are passed as parameters

    //     // Get all sectors
    //     $allSectors = Sector::pluck('id')->toArray();
     
    //     foreach ($allSectors as $sector) {
    //         // Get all points available for this sector
    //         $allAvailablePoints = Grouppoint::where('sector_id', $sector)->where('deleted', 0)
    //             ->select('government_id', 'id', 'points_ids')
    //             ->get();

    //         // Get all groups and the number of points for each group in the sector
    //         $allGroupsForSector = Groups::where('sector_id', $sector)
    //             ->select('id', 'points_inspector')
    //             ->get();

    //         // Mapping to store government_id for each point
    //         $groupGovernmentIds = [];
    //         foreach ($allAvailablePoints as $point) {
    //             $groupGovernmentIds[$point->id] = $point->government_id;
    //         }

    //         foreach ($allGroupsForSector as $group) {
    //             // Get all teams of this group and their history for yesterday
    //             $groupTeams = InspectorMission::where('group_id', $group->id)
    //                 ->select('group_team_id', 'ids_group_point')
    //                 ->whereDate('date', $yesterday)
    //                 ->distinct('group_team_id')
    //                 ->get();

    //             // Check if the groupTeams for yesterday is empty
    //             if ($groupTeams->isEmpty()) {

    //                 // If empty, get the teams for today and initialize ids_group_point to an empty array
    //                 $groupTeams = InspectorMission::where('group_id', $group->id)
    //                     ->select('group_team_id', 'ids_group_point')
    //                     ->whereDate('date', $today)
    //                     ->distinct('group_team_id')
    //                     ->get();
    //                 // dd($today,$groupTeams,$groupTeams->isEmpty());
    //                 // continue;
    //             }


    //             // Initialize variables
    //             $teamPointsYesterday = [];
    //             $teamPointsToday = [];
    //             $dayOffTeams = [];

    //             foreach ($groupTeams as $groupTeam) {
    //                 // Store yesterday's points
    //                 $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ? $groupTeam->ids_group_point : [];

    //                 // Points per team
    //                 $pointPerTeam = $group->points_inspector;

    //                 // Collect day-off teams
    //                 $teamsWithDayOff = InspectorMission::where('group_id', $group->id)
    //                     ->where('group_team_id', $groupTeam->group_team_id)
    //                     ->where('date', $today)
    //                     ->where('day_off', 1)
    //                     ->pluck('id')
    //                     ->toArray();

    //                 if (!empty($teamsWithDayOff)) {
    //                     $dayOffTeams = array_merge($dayOffTeams, $teamsWithDayOff);
    //                     continue;
    //                 }

    //                 // Filter available points based on the government_id of Grouppoint
    //                 $availablePoints = $allAvailablePoints->pluck('id')->toArray();
    //                 $filteredAvailablePoints = array_filter($availablePoints, function ($pointId) use ($groupGovernmentIds) {
    //                     return isset($groupGovernmentIds[$pointId]);
    //                 });

    //                 $availablePoints = $filteredAvailablePoints;

    //                 // Remove points assigned yesterday
    //                 $availablePoints = array_diff($availablePoints, $teamPointsYesterday[$groupTeam->group_team_id] ?? []);

    //                 if (!empty($availablePoints)) {
    //                     // Ensure the required number of points is selected
    //                     $possibleNewValues = array_splice($availablePoints, 0, $pointPerTeam);

    //                     // Ensure all new points are from the same government
    //                     $pointGovernmentIds = array_map(function ($pointId) use ($groupGovernmentIds) {
    //                         return $groupGovernmentIds[$pointId] ?? null;
    //                     }, $possibleNewValues);

    //                     if (count(array_unique($pointGovernmentIds)) > 1) {
    //                         // Points are from different governments, reshuffle and try again
    //                         $possibleNewValues = array_slice($availablePoints, 0, $pointPerTeam);
    //                     }

    //                     // Assign new values to the team
    //                     $teamPointsToday[$groupTeam->group_team_id] = $possibleNewValues;

    //                     // Update missions with new points
    //                     $updatedMissions = InspectorMission::where('group_id', $group->id)
    //                         ->where('group_team_id', $groupTeam->group_team_id)
    //                         ->where('date', $today)
    //                         ->where('day_off', 0)
    //                         ->pluck('id')
    //                         ->toArray();

    //                     foreach ($updatedMissions as $updatedMission) {
    //                         $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
    //                         if ($updated) {
    //                             // Update the ids_group_point field
    //                             $updated->ids_group_point = array_map('strval', $possibleNewValues);
    //                             $updated->save();

    //                             // Call notification function to notify the inspector with today's points
    //                         }
    //                     }

    //                     // Remove the assigned points from available points
    //                     $allAvailablePoints = $allAvailablePoints->whereNotIn('id', $possibleNewValues);
    //                 } else {
    //                     // Clear ids_group_point field as no points are available
    //                     $updatedMissions = InspectorMission::where('group_id', $group->id)
    //                         ->where('group_team_id', $groupTeam->group_team_id)
    //                         ->where('date', $today)
    //                         ->pluck('id')
    //                         ->toArray();

    //                     foreach ($updatedMissions as $updatedMission) {
    //                         $updated = InspectorMission::find($updatedMission);
    //                         if ($updated) {
    //                             $updated->ids_group_point = [];
    //                             $updated->save();
    //                         }
    //                     }
    //                 }
    //             }

    //             // Switch points if the same points were assigned yesterday
    //             foreach ($teamPointsYesterday as $teamId => $points) {
    //                 if (in_array($points, $teamPointsYesterday, true)) {
    //                     // Find a different team with matching points
    //                     foreach ($teamPointsToday as $otherTeamId => $otherPoints) {
    //                         if ($teamId !== $otherTeamId && $points === $teamPointsYesterday[$otherTeamId]) {
    //                             // Swap points
    //                             InspectorMission::where('group_team_id', $teamId)
    //                                 ->where('date', $today)
    //                                 ->update(['ids_group_point' => array_map('strval', $otherPoints)]);

    //                             InspectorMission::where('group_team_id', $otherTeamId)
    //                                 ->where('date', $today)
    //                                 ->update(['ids_group_point' => array_map('strval', $points)]);

    //                             // Update the team points
    //                             $teamPointsToday[$teamId] = $otherPoints;
    //                             $teamPointsToday[$otherTeamId] = $points;

    //                             break;
    //                         }
    //                     }
    //                 }
    //             }

    //             foreach ($dayOffTeams as $dayOffTeam) {
    //                 $updated = InspectorMission::find($dayOffTeam);
    //                 if ($updated) {
    //                     $updated->ids_group_point = [];
    //                     $updated->save();
    //                 }
    //             }
    //         }
    //     }
    // }
}
