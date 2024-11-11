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
    protected $sector;
    protected $group;

    public function __construct(Carbon $startOfMonth, Carbon $endOfMonth, $sector, $group)
    {

        // if ($WorkingTreeTime) {
        //     $working_time = WorkingTime::find($WorkingTreeTime->working_time_id);

        //     // Check if current time is within today's working hours
        //     if ($working_time && $currentTime >= $working_time->start_time && $currentTime <= $working_time->end_time) {
        //         // Start changes from tomorrow instead of today
        //         $date = $next_day_date;
        //     } else {
        //         // Start changes from today
        //         $date = $start_day_date;
        //     }
        // }
        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;
        $this->sector = $sector;
        $this->group = $group;
    }

    public function handle()
    {
        $startOfMonth = $this->startOfMonth;
        $endOfMonth = $this->endOfMonth;
        $sector = $this->sector;
        $group = $this->group;

        while ($startOfMonth->lte($endOfMonth)) {
            $today = $startOfMonth->toDateString();
            $yesterday = $startOfMonth->copy()->subDay()->toDateString();
            $this->teamOfGroup($yesterday, $today, $sector, $group);
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

    public function getAvailablePoints($index, $sector, $group, $team, $teamTimePeriods, $historyOfTeam, $pointCount, $userToday = null, $history = null)
    {
        $idsOfHistory = [];
        $idsOfTodayUsed = [];
        $validPoints = [];
        $idsOfHistoryGroups = [];
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
        if ($history) {
            $idsOfHistoryGroups = Grouppoint::whereIn('id', $history)
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
        if (count($idsOfHistoryGroups) > 0) {
            $allPoints->whereNotIn('id', $idsOfHistoryGroups);
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

    function isTimeAvailable($pointStart, $pointEnd, $teamStart, $teamEnd)
    {
        $pointStartTimestamp = strtotime($pointStart);
        $pointEndTimestamp = strtotime($pointEnd);
        $teamStartTimestamp = strtotime($teamStart);
        $teamEndTimestamp = strtotime($teamEnd);

        return $teamStartTimestamp <= $pointStartTimestamp && $teamStartTimestamp >= $pointEndTimestamp && $teamEndTimestamp >= $pointStartTimestamp;
    }
    function countOfPoints($sector)
    {
        $groups = Groups::where('sector_id', $sector)->get();
        $points = Grouppoint::where('deleted', 0)->where('sector_id', $sector)->count();
        $teamCount = 0;

        foreach ($groups as $group) {
            $teamCount += GroupTeam::where('group_id', $group->id)->count();
        }
        return floor($points / $teamCount);
    }

    public function teamOfGroup($yesterday, $today, $sector, $group)
    {
        $todayIndex = $this->todayIndex($today);
        $allGroups = Groups::where('sector_id', $sector)->whereNot('id', $group)->pluck('id')->toArray();

        $historyPoints = [];
        foreach ($allGroups as $allGroup) {
            $points = InspectorMission::where('group_id', $allGroup)
                ->whereDate('date', $today)
                ->distinct('group_team_id')
                ->pluck('ids_group_point')
                ->flatten()
                ->toArray();

            $historyPoints = array_merge($historyPoints, $points);
        }

        $Group = Groups::findOrFail($group);

        $teams = GroupTeam::where('group_id', $group)->pluck('id')->toArray();
        $groupTeams = InspectorMission::where('group_id', $group)
            ->whereIn('group_team_id', $teams)
            ->select('group_team_id', 'ids_group_point')
            ->whereDate('date', $yesterday)
            ->distinct('group_team_id')
            ->get();

        if ($groupTeams->isEmpty()) {

            $groupTeams = InspectorMission::where('group_id', $group)
                ->whereIn('group_team_id', $teams)
                ->select('group_team_id', 'ids_group_point')
                ->whereDate('date', $today)
                ->distinct('group_team_id')
                ->get();
        }

        $teamPointsCount = $groupTeams->mapWithKeys(function ($team) {
            return [
                $team->group_team_id => count($team->ids_group_point ?? [])
            ];
        })->toArray();

        $dayOffTeams = [];
        $usedPointsToday = [];
        $groupTeams = $groupTeams->shuffle();

        foreach ($groupTeams as $groupTeam) {
            $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ?: [];
            //$pointPerTeam = $Group->points_inspector;
            $pointPerTeam = $this->countOfPoints($sector->id);

            $teamsWorkingTime = InspectorMission::with('workingTime')
                ->where('group_id', $group)
                ->where('group_team_id', $groupTeam->group_team_id)
                ->whereDate('date', $today)
                ->where('day_off', 0)
                ->distinct('group_team_id')
                ->get();

            $teamTimePeriods = $teamsWorkingTime->map(function ($mission) {
                return [$mission->workingTime->start_time, $mission->workingTime->end_time];
            })->toArray();
            // $currentTime = Carbon::now();

            // $isWithinWorkingTime = $teamsWorkingTime->contains(function ($mission) use ($currentTime) {
            //     $startTime = Carbon::parse($mission->workingTime->start_time);
            //     $endTime = Carbon::parse($mission->workingTime->end_time);

            //     return $currentTime->between($startTime, $endTime);
            // });
            // if (!$isWithinWorkingTime) {
            //     $nextDayIndex = ($todayIndex + 1) % 7;

            //     $teamsWorkingTime = InspectorMission::with('workingTime')
            //         ->where('group_id', $group)
            //         ->where('group_team_id', $groupTeam->group_team_id)
            //         ->whereDate('date', $today)
            //         ->where('day_off', 0)
            //         ->distinct('group_team_id')
            //         ->get();

            //     $teamTimePeriods = $teamsWorkingTime->map(function ($mission) {
            //         return [$mission->workingTime->start_time, $mission->workingTime->end_time];
            //     })->toArray();
            // }
            $teamsWithDayOff = InspectorMission::where('group_id', $group)
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
                $pointOfTeam = $this->getAvailablePoints($todayIndex, $sector, $group, $groupTeam->group_team_id, $teamTimePeriods, $teamPointsYesterday[$groupTeam->group_team_id], $pointPerTeam, $usedPointsToday, $historyPoints);
            }
            $usedPointsToday = array_merge($usedPointsToday, $pointOfTeam);

            $updatedMissions = InspectorMission::where('group_id', $group)
                ->where('group_team_id', $groupTeam->group_team_id)
                ->whereDate('date', $today)
                ->where('day_off', 0)
                ->pluck('id')
                ->toArray();

            foreach ($updatedMissions as $updatedMission) {
                $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
                if ($updated) {
                    $updated->ids_group_point = array_map('strval', $pointOfTeam);
                    $updated->save();
                }
            }

            $teamPointsCount[$groupTeam->group_team_id] = ($teamPointsCount[$groupTeam->group_team_id] ?? 0) + count($pointOfTeam);
        }
    }
}
