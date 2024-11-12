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
            $this->getTeamsTimes($yesterday, $today);
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
        $allTeamTimes = [];

        foreach ($sectors as $sector) {
            $allGroups = Groups::where('sector_id', $sector->id)->pluck('id')->toArray();

            foreach ($allGroups as $group) {
                $teams = GroupTeam::where('group_id', $group)->pluck('id')->toArray();

                // Fetch missions for yesterday
                $groupTeams = InspectorMission::where('group_id', $group)
                    ->whereIn('group_team_id', $teams)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                // If no missions found for yesterday, try today
                if ($groupTeams->isEmpty()) {
                    $groupTeams = InspectorMission::where('group_id', $group)
                        ->whereIn('group_team_id', $teams)
                        ->select('group_team_id', 'ids_group_point')
                        ->whereDate('date', $today)
                        ->distinct('group_team_id')
                        ->get();
                }

                // Log if no missions are found
                if ($groupTeams->isEmpty()) {
                    \Log::info("No missions found for group ID: {$group} on {$yesterday} or {$today}");
                    continue;
                }

                $teamPointsCount = $groupTeams->mapWithKeys(function ($team) {
                    return [
                        $team->group_team_id => count($team->ids_group_point ?? [])
                    ];
                })->toArray();

                $dayOffTeams = [];
                $pointOfTeam = [];
                $groupTeams = $groupTeams->shuffle(); // Optional: You may decide if shuffling is really necessary

                foreach ($groupTeams as $groupTeam) {
                    $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ?: [];

                    // Example of counting points per team (if relevant)
                    $pointPerTeam = $this->countOfPoints($sector->id);

                    // Fetch working times for the team
                    $teamsWorkingTime = InspectorMission::with('workingTime')
                        ->where('group_id', $group)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 0) // Ensure it's not a day off
                        ->distinct('group_team_id')
                        ->get();

                    // Collect working time periods for the team
                    $teamTimePeriods = $teamsWorkingTime->map(function ($mission) {
                        return [
                            'start_time' => $mission->workingTime->start_time,
                            'end_time' => $mission->workingTime->end_time
                        ];
                    })->toArray();

                    // Fetch teams with days off
                    $teamsWithDayOff = InspectorMission::where('group_id', $group)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 1)
                        ->pluck('id')
                        ->toArray();

                    // Add the results for each team to the final array
                    $allTeamTimes[] = [
                        'sector_id' => $sector->id,
                        'group_id' => $group,
                        'team_id' => $groupTeam->group_team_id,
                        'working_times' => $teamTimePeriods,
                        'teams_with_day_off' => $teamsWithDayOff,
                    ];
                }
            }
        }

        dd($allTeamTimes) ; // Return or process this array as needed
    }

}
