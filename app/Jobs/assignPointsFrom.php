<?php

namespace App\Jobs;

use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\GroupTeam;
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
     * Execute the job./-
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
    
        $todayDate = Carbon::parse($today);
        $dayWeek = $todayDate->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);
    
        $allSectors = Sector::pluck('id')->toArray();
        $groupGovernmentIds=[];
        foreach ($allSectors as $sector) {
            // if($sector == 1) { // Fixed condition check
                $allAvailablePoints = Grouppoint::where('sector_id', $sector)
                    ->where('deleted', 0)
                    ->select('government_id', 'id', 'points_ids')
                    ->get();
    
                foreach ($allAvailablePoints as $grouppoint) {
                    $available_points = Point::with('pointDays')->whereIn('id', $grouppoint->points_ids)->get();
    
                    foreach ($available_points as $available_point) {
                        if ($available_point->work_type == 0) {
                            $is_off = in_array($index, $available_point->days_work);
                            if ($is_off) {
                                // Assign point if work_type is 0
                                $groupGovernmentIds[$available_point->id] = [
                                    'id' => $available_point->id,
                                    'government_id' => $available_point->government_id,
                                    'grouppoint_id' => $grouppoint->id,
                                    'work_type' => 0,
                                ];
                            }
                        } else {
                            $pointDay = $available_point->pointDays->where('name', $index)->first();
                            if ($pointDay) {
                                // Assign point if work_type is 1
                                $groupGovernmentIds[$available_point->id] = [
                                    'id' => $available_point->id,
                                    'government_id' => $available_point->government_id,
                                    'grouppoint_id' => $grouppoint->id,
                                    'work_type' => 1,
                                    'point_time' => [$pointDay->from, $pointDay->to],
                                ];
                            }
                        }
                    }
                }
                // dd($groupGovernmentIds);
                $allGroupsForSector = Groups::where('sector_id', $sector)
                    ->select('id', 'points_inspector')
                    ->get();
    
                foreach ($allGroupsForSector as $group) {
                    $teams = GroupTeam::where('group_id', $group->id)->pluck('id')->toArray();
                    $groupTeams = InspectorMission::where('group_id', $group->id)->whereIn('group_team_id', $teams)
                        ->select('group_team_id', 'ids_group_point')
                        ->whereDate('date', $yesterday)
                        ->distinct('group_team_id')
                        ->get();
    
                    if ($groupTeams->isEmpty()) {
                        $groupTeams = InspectorMission::where('group_id', $group->id)->whereIn('group_team_id', $teams)
                            ->select('group_team_id', 'ids_group_point')
                            ->whereDate('date', $today)
                            ->distinct('group_team_id')
                            ->get();
                    }
    
                    $teamPointsYesterday = [];
                    $teamPointsToday = [];
                    $dayOffTeams = [];
    
                    foreach ($groupTeams as $groupTeam) {
                        $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ? $groupTeam->ids_group_point : [];
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
    
                        $filteredAvailablePoints = array_filter($groupGovernmentIds, function ($point) use ($group) {
                            return $point['government_id'] == $group->government_id;
                        });
    
                        if (empty($filteredAvailablePoints)) {
                            $firstGovernmentId = reset($groupGovernmentIds)['government_id'] ?? null;
    
                            if ($firstGovernmentId) {
                                $filteredAvailablePoints = array_filter($groupGovernmentIds, function ($point) use ($firstGovernmentId) {
                                    return $point['government_id'] == $firstGovernmentId;
                                });
                            }
                        }
    
                        $availablePoints = array_keys($filteredAvailablePoints);
                        $availablePoints = array_diff($availablePoints, $teamPointsYesterday[$groupTeam->group_team_id] ?? []);
                        if (!empty($availablePoints)) {
                            $possibleNewValues = array_splice($availablePoints, 0, $pointPerTeam);
    
                            $pointGovernmentIds = array_map(function ($pointId) use ($groupGovernmentIds) {
                                return $groupGovernmentIds[$pointId]['government_id'] ?? null;
                            }, $possibleNewValues);
    
                            if (count(array_unique($pointGovernmentIds)) > 1) {
                                $possibleNewValues = array_slice($availablePoints, 0, $pointPerTeam);
                            }
    
                            $validPoints = [];
                            foreach ($possibleNewValues as $pointId) {
                                if (isset($groupGovernmentIds[$pointId])) {
                                    $point = $groupGovernmentIds[$pointId];
    
                                    if ($point['work_type'] == 0) {
                                        $validPoints[] = $pointId;
                                    } else {
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
                            $availableGrouppointIds = [];

                            foreach ($validPoints as $pointId) {
                                if (isset($groupGovernmentIds[$pointId])) {
                                    $availableGrouppointIds[] = $groupGovernmentIds[$pointId]['grouppoint_id'];
                                }
                            }
                            
                            // Now $availableGrouppointIds contains only the grouppoint_ids without point_ids.
                            $availableGrouppointIds = array_unique($availableGrouppointIds); // Ensure unique grouppoint_ids
                            $teamPointsToday[$groupTeam->group_team_id] = $availableGrouppointIds;
    
                            $updatedMissions = InspectorMission::where('group_id', $group->id)
                                ->where('group_team_id', $groupTeam->group_team_id)
                                ->whereDate('date', $today)
                                ->where('day_off', 0)
                                ->pluck('id')
                                ->toArray();
    
                            foreach ($updatedMissions as $updatedMission) {
                                $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
                                if ($updated) {
                                    $updated->ids_group_point = array_map('strval', $availableGrouppointIds);
                                    $updated->save();
                                }
                            }
    
                            $groupGovernmentIds = array_filter($groupGovernmentIds, function ($point) use ($validPoints) {
                                return !in_array($point['id'], $validPoints);
                            });
                        }
                    }
                }
            // }
        }
    }
}
