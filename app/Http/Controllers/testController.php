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
    //function to get working times for team for today

    public function teamOfGroup($yesterday, $today)
    {
        $todayIndex = $this->todayIndex($today);
        $allSectors = Sector::all();

        // Initialize an array to store working times for each team
        $sectorTeamWorkingTimes = [];

        foreach ($allSectors as $sector) {
            // Fetch all groups within the sector
            $allGroups = Groups::where('sector_id', $sector->id)->get();

            // Loop through each group in the sector
            foreach ($allGroups as $group) {
                $teams = GroupTeam::where('group_id', $group->id)->pluck('id')->toArray();

                // Get group team missions for yesterday and today
                $groupTeams = InspectorMission::where('group_id', $group->id)
                    ->whereIn('group_team_id', $teams)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                // If no missions found for yesterday, fetch them for today
                if ($groupTeams->isEmpty()) {
                    $groupTeams = InspectorMission::where('group_id', $group->id)
                        ->whereIn('group_team_id', $teams)
                        ->select('group_team_id', 'ids_group_point')
                        ->whereDate('date', $today)
                        ->distinct('group_team_id')
                        ->get();
                }

                // Initialize the array for the sector
                if (!isset($sectorTeamWorkingTimes[$sector->id])) {
                    $sectorTeamWorkingTimes[$sector->id] = [];
                }

                // Loop through each team
                foreach ($groupTeams as $groupTeam) {
                    // Initialize array for team if not already set
                    if (!isset($sectorTeamWorkingTimes[$sector->id][$groupTeam->group_team_id])) {
                        $sectorTeamWorkingTimes[$sector->id][$groupTeam->group_team_id] = [
                            'team_id' => $groupTeam->group_team_id,
                            'working_times' => []
                        ];
                    }

                    // Get the working times for the team on the given day (today)
                    $teamsWorkingTime = InspectorMission::with('workingTime')
                        ->where('group_id', $group->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 0)
                        ->distinct('group_team_id')
                        ->get();

                    // Add the working times (start_time, end_time) to the team entry
                    foreach ($teamsWorkingTime as $mission) {
                        $sectorTeamWorkingTimes[$sector->id][$groupTeam->group_team_id]['working_times'][] = [
                            'start_time' => $mission->workingTime->start_time,
                            'end_time' => $mission->workingTime->end_time,
                        ];
                    }

                    // Get teams with a day off and skip them for today
                    $teamsWithDayOff = InspectorMission::where('group_id', $group->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 1)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($teamsWithDayOff)) {
                        continue; // Skip teams with a day off
                    }
                }
            }
        }

        // Output or further process the collected working times for all teams in each sector
        dd($sectorTeamWorkingTimes);
    }

}
