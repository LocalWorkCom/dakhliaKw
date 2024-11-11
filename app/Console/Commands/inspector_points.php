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

            "الأحد",
            "الاثنين",
            "الثلاثاء",
            "الأربعاء",
            "الخميس",
            "الجمعة",
            "السبت",
        ];

        $todayDate = Carbon::parse($today);
        $dayWeek = $todayDate->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);

        return $index !== false ? $index : null;
    }

    public function getAvailablePoints($index, $sector, $group, $team, $teamTimePeriods, $historyOfTeam, $pointCount, $userToday = null, $usedPointsGroupToday = null)
    {
        $idsOfHistory = [];
        $idsOfTodayUsed = [];
        $validPoints = [];
        $idsOfTodayUsedGroup = [];
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
        if ($usedPointsGroupToday) {
            $idsOfTodayUsedGroup = Grouppoint::whereIn('id', $usedPointsGroupToday)
                ->pluck('points_ids')
                ->flatten()
                ->toArray();
        }
        $allPoints = Point::with('pointDays')->where('sector_id', $sector);
        if (count($idsOfTodayUsed) > 0) {
            $allPoints->whereNotIn('id', $idsOfTodayUsed);
        }
        if (count($idsOfTodayUsedGroup) > 0) {
            $allPoints->whereNotIn('id', $idsOfTodayUsedGroup);
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

       // dd(ceil($points / max($teamCount, 1)));
        return ceil($points / max($teamCount, 1));
    }


    public function teamOfGroup($yesterday, $today)
    {
        $todayIndex = $this->todayIndex($today);
        $allSectors = Sector::all();

        foreach ($allSectors as $sector) {
            $allGroups = Groups::where('sector_id', $sector->id)->get();
            $usedPointsGroupToday = [];
            $usedPointsToday = [];
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
                $pointOfTeam = [];
                $groupTeams = $groupTeams->shuffle();

                foreach ($groupTeams as $groupTeam) {
                    $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ?: [];
                    $pointPerTeam = $group->points_inspector;
                    //$pointPerTeam = $this->countOfPoints($sector->id);
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
                        $pointOfTeam = $this->getAvailablePoints($todayIndex, $sector->id, $group->id, $groupTeam->group_team_id, $teamTimePeriods, $teamPointsYesterday[$groupTeam->group_team_id], $pointPerTeam, $usedPointsToday, $usedPointsGroupToday);
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
                }
            }
        }
    }
}
