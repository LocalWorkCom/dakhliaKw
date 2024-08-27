<?php

namespace App\Jobs;

use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\InspectorMission;
use App\Models\Point;
use App\Models\Sector;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class assignPointsFrom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startOfMonth;
    protected $endOfMonth;

    public function __construct(Carbon $startOfMonth, Carbon $endOfMonth)
    {
        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;
    }

    /**
     * Execute the job.
     */
    function overlaps($range1, $range2)
    {
        $range1 = array_map(function ($time) {
            return $this->normalizeTime($time);
        }, $range1);
        $range2 = array_map(function ($time) {
            return $this->normalizeTime($time);
        }, $range2);

        // Convert time strings to Carbon instances
        $start1 = Carbon::createFromFormat('H:i:s', $range1[0]);
        $end1 = Carbon::createFromFormat('H:i:s', $range1[1]);
        $start2 = Carbon::createFromFormat('H:i:s', $range2[0]);
        $end2 = Carbon::createFromFormat('H:i:s', $range2[1]);
        return $start1 < $end2 && $start2 < $end1;
    }

    function normalizeTime($time)
    {
        // Add seconds if missing and parse time
        try {
            return Carbon::createFromFormat('H:i:s', $time)->format('H:i:s');
        } catch (\Exception $e) {
            // If seconds are missing, add them and parse again
            return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
        }
    }
    public function handle(Carbon $startOfMonth = null, Carbon $endOfMonth = null)
    {
        $startOfMonth = $startOfMonth ?? Carbon::now()->startOfMonth();
        $endOfMonth = $endOfMonth ?? Carbon::now()->endOfMonth();

        while ($startOfMonth->lte($endOfMonth)) {
            // Define dates for this iteration
            $today = $startOfMonth->toDateString();
            $yesterday = $startOfMonth->copy()->subDay()->toDateString();

            // Debugging information
            // $this->info("Processing Date - Today: $today, Yesterday: $yesterday");

            // Run your function for these dates
            $this->fetchPoints($yesterday, $today);

            // Move to the next day
            $startOfMonth->addDay();
        }
    }


    public function fetchPoints($yesterday, $today)
    {
        // Define days of the week in Arabic
        $daysOfWeek = [
            "السبت",
            "الأحد",
            "الاثنين",
            "الثلاثاء",
            "الأربعاء",
            "الخميس",
            "الجمعة",
        ];

        // Parse today's date and get the day of the week in Arabic
        $todayDate = Carbon::parse($today);
        $dayWeek = $todayDate->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek); // Get index of today

        // Get all sector IDs
        $allSectors = Sector::pluck('id')->toArray();
        $groupGovernmentIds = [];

        // Collect available points for each sector
        foreach ($allSectors as $sector) {
            $allAvailablePoints = Grouppoint::where('sector_id', $sector)
                ->where('deleted', 0)
                ->select('government_id', 'id', 'points_ids')
                ->get();

            foreach ($allAvailablePoints as $grouppoint) {
                $available_points = Point::with('pointDays')->whereIn('id', $grouppoint->points_ids)->get();

                foreach ($available_points as $available_point) {
                    if ($available_point->work_type == 0) {
                        $groupGovernmentIds[$available_point->id] = [
                            'id' => $available_point->id,
                            'government_id' => $available_point->government_id,
                            'work_type' => 0,
                        ];
                    } else {
                        $pointDay = $available_point->pointDays->where('name', $index)->first();

                        if ($pointDay) {
                            $groupGovernmentIds[$available_point->id] = [
                                'id' => $available_point->id,
                                'government_id' => $available_point->government_id,
                                'work_type' => 1,
                                'point_time' => [$pointDay->from, $pointDay->to],
                            ];
                        }
                    }
                }
            }

            // Get all groups for the sector
            $allGroupsForSector = Groups::where('sector_id', $sector)
                ->select('id', 'points_inspector')
                ->get();

            foreach ($allGroupsForSector as $group) {
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->select('group_team_id', 'ids_group_point')
                    ->where('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                if ($groupTeams->isEmpty()) {
                    $groupTeams = InspectorMission::where('group_id', $group->id)
                        ->select('group_team_id', 'ids_group_point')
                        ->whereDate('date', $today)
                        ->distinct('group_team_id')
                        ->get();
                }

                $teamPointsYesterday = [];
                $teamPointsToday = [];
                $dayOffTeams = [];

                foreach ($groupTeams as $groupTeam) {
                    // Store yesterday's points
                    $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ? $groupTeam->ids_group_point : [];
                    // Number of points needed for each team
                    $pointPerTeam = $group->points_inspector;

                    // Get the working time periods for the team
                    $teamsWorkingTime = InspectorMission::with('workingTime')
                        ->where('group_id', $group->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 0)
                        ->get();

                    $teamTimePeriods = $teamsWorkingTime->map(function ($mission) {
                        return [$mission->workingTime->start_time, $mission->workingTime->end_time];
                    })->toArray();

                    // Collect IDs of teams with day off
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

                    // Filter available points based on government ID
                    $filteredAvailablePoints = array_filter($groupGovernmentIds, function ($point) use ($group) {
                        return $point['government_id'] == $group->government_id;
                    });

                    if (empty($filteredAvailablePoints)) {
                        // Fallback: If no points are available from the same government,
                        // find the first available government and filter points for that government
                        $firstGovernmentId = reset($groupGovernmentIds)['government_id'] ?? null;

                        if ($firstGovernmentId) {
                            $filteredAvailablePoints = array_filter($groupGovernmentIds, function ($point) use ($firstGovernmentId) {
                                return $point['government_id'] == $firstGovernmentId;
                            });
                        }
                    }
                    // dd($filteredAvailablePoints);
                    $availablePoints = array_keys($filteredAvailablePoints);

                    // Remove points already assigned to the team yesterday
                    $availablePoints = array_diff($availablePoints, $teamPointsYesterday[$groupTeam->group_team_id] ?? []);

                    if (!empty($availablePoints)) {
                        $possibleNewValues = array_splice($availablePoints, 0, $pointPerTeam);

                        // Ensure points are from the same government
                        $pointGovernmentIds = array_map(function ($pointId) use ($groupGovernmentIds) {
                            return $groupGovernmentIds[$pointId]['government_id'] ?? null;
                        }, $possibleNewValues);

                        if (count(array_unique($pointGovernmentIds)) > 1) {
                            // Points are from different governments, but it's the best we can assign
                            $possibleNewValues = array_slice($availablePoints, 0, $pointPerTeam);
                        }

                        // Further filter points based on work_type and time
                        $validPoints = [];
                        foreach ($possibleNewValues as $pointId) {
                            if (isset($groupGovernmentIds[$pointId])) {
                                $point = $groupGovernmentIds[$pointId];

                                if ($point['work_type'] == 0) {
                                    $validPoints[] = $pointId;
                                } else {
                                    // Check if point's time period overlaps with the team’s working time
                                    $pointTimeRange = $point['point_time'] ?? [];
                                    foreach ($teamTimePeriods as $teamTime) {
                                        if ($this->overlaps($pointTimeRange, $teamTime)) {
                                            $validPoints[] = $pointId;
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        // Assign points to the team
                        $teamPointsToday[$groupTeam->group_team_id] = $validPoints;

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
                                $updated->ids_group_point = array_map('strval', $validPoints);
                                $updated->save();
                            }
                        }


                        // Remove the assigned points from available points
                        $groupGovernmentIds = array_filter($groupGovernmentIds, function ($point) use ($validPoints) {
                            return !in_array($point['id'], $validPoints);
                        });
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
                        foreach ($teamPointsToday as $otherTeamId => $otherPoints) {
                            if ($teamId !== $otherTeamId && $points === $teamPointsYesterday[$otherTeamId]) {
                                // Swap points
                                InspectorMission::where('group_team_id', $teamId)
                                    ->where('date', $today)
                                    ->update(['ids_group_point' => array_map('strval', $otherPoints)]);

                                InspectorMission::where('group_team_id', $otherTeamId)
                                    ->where('date', $today)
                                    ->update(['ids_group_point' => array_map('strval', $points)]);

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
}
