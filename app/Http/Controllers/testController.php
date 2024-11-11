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
use App\Models\Point;
use App\Models\Sector;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class testController extends Controller
{
    public function index()
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
    //function to get working times for team for today
    public function getTeamsTimes($yesterday, $today)
    {
        $sectors = Sector::all();
        foreach ($sectors as $sector) {
            $allGroups = Groups::where('sector_id', $sector->id)->pluck('id')->toArray();
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
                    //$pointPerTeam = $group->points_inspector;
                    $pointPerTeam = $this->countOfPoints($sector->id);
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
                }
            }
        }
    }
}
