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
    function isTimeAvailable($pointStart, $pointEnd, $teamStart, $teamEnd)
    {
        $pointStartTimestamp = strtotime($pointStart);
        $pointEndTimestamp = strtotime($pointEnd);
        $teamStartTimestamp = strtotime($teamStart);
        $teamEndTimestamp = strtotime($teamEnd);
        // Normalize the team’s working hours in case they span across midnight
        if ($teamEndTimestamp <= $teamStartTimestamp) {
            // If the team's end time is less than the start time, it spans over midnight
            $teamEndTimestamp += 24 * 60 * 60; // Add one day (24 hours) to the end time
        }
        // Normalize the point's working hours if necessary (in case it's overnight as well)
        if ($pointEndTimestamp <= $pointStartTimestamp) {
            $pointEndTimestamp += 24 * 60 * 60; // Add one day (24 hours) to the end time
        }
        // Check for overlap
        return ($pointStartTimestamp < $teamEndTimestamp && $pointEndTimestamp > $teamStartTimestamp);
    }

    function countOfPoints($sector, $today)
    {
        $groups = Groups::where('sector_id', $sector)->get();
        $teamCount = 0;
        // Calculate number of teams for the sector
        foreach ($groups as $group) {
            $teamCount += GroupTeam::where('group_id', $group->id)->count();
        }
        // Get the number of valid points available today
        $pointsAvailableToday = grouppoint::where('sector_id', $sector)->where('deleted', 0)->count();

        return $teamCount == 0 ? 0 : floor($pointsAvailableToday / $teamCount);
    }
    public function teamOfGroup($yesterday, $today)
    {
        $todayIndex = $this->todayIndex($today);
        $allSectors = Sector::all();

        foreach ($allSectors as $sector) {
            $allGroups = Groups::where('sector_id', $sector->id)->get();
            $usedPointsToday = []; // Track assigned points for today
            $allGroups = $allGroups->shuffle();
            foreach ($allGroups as $group) {
                $teams = GroupTeam::where('group_id', $group->id)->get();
                $teams = $teams->shuffle();

                foreach ($teams as $team) {
                    // Get team's working times
                    $teamWorkingTime = $this->getTeamWorkingTime($team->id, $today);
                    $availablePoints = $this->getPointsForToday($sector->id, $todayIndex, $usedPointsToday);

                    $assignedPoints = [];
                    foreach ($availablePoints as $point) {
                        // Check if point's working time overlaps with team's working time
                        if ($this->isTimeAvailable(
                            $point['start_time'],
                            $point['end_time'],
                            $teamWorkingTime['start_time'],
                            $teamWorkingTime['end_time']
                        ) && !in_array($point['group_point_id'], $usedPointsToday)) {

                            // Assign point to team and mark it as used
                            $assignedPoints[] = $point['group_point_id'];
                            $usedPointsToday[] = $point['group_point_id'];

                            // Break loop if team has enough points for today
                            if (count($assignedPoints) >= $this->countOfPoints($sector->id, $today)) {
                                break;
                            }
                        }
                    }

                    // Assign points to team for today
                    if (!empty($assignedPoints)) {
                        $this->assignPointsToTeam($group->id, $team->id, $today, $assignedPoints);
                    }
                }
            }
        }
    }

    private function getPointsForToday($sectorId, $todayIndex, $assignedGroupPoints)
    {
        // Initialize the available points array
        $availablePoints = [];

        // Fetch all the group points related to the sector, excluding those already assigned today
        $allGroupspoints = Grouppoint::where('sector_id', $sectorId)
            ->whereNotIn('id', $assignedGroupPoints) // Exclude assigned Grouppoints
            ->where('deleted', 0)
            ->get();

        // Loop through each group point
        foreach ($allGroupspoints as $allGroupspoint) {
            $points_ids = $allGroupspoint->points_ids;

            // Retrieve all points in a single query
            $points = Point::whereIn('id', $points_ids)->get()->shuffle();

            // Loop through each point in the group point
            foreach ($points as $point) {
                // Check for the work type and available days
                if ($point->work_type == 0) {
                    // Check if today is a valid work day for this point
                    if (in_array($todayIndex, $point->days_work)) {
                        // Add this point to the available points
                        $availablePoints[] = [
                            'group_point_id' => $allGroupspoint->id,
                            'point_id' => $point->id,
                            'start_time' => '00:00',
                            'end_time' => '23:00'
                        ];

                        // Mark the Grouppoint as assigned for today
                        $assignedGroupPoints[] = $allGroupspoint->id;

                        // break; // Exit inner loop to avoid duplicate points for the same Grouppoint
                    }
                } else {
                    // Check if the point has a specific point day for today
                    $pointDay = $point->pointDays->where('name', $todayIndex)->first();
                    if ($pointDay) {
                        // Add point to available points if it's suitable
                        $availablePoints[] = [
                            'group_point_id' => $allGroupspoint->id,
                            'point_id' => $point->id,
                            'start_time' => $pointDay->from,
                            'end_time' => $pointDay->to
                        ];

                        $assignedGroupPoints[] = $allGroupspoint->id;

                        // break; // Exit inner loop to avoid duplicate points for the same Grouppoint
                    }
                }
            }
        }

        return $availablePoints;
    }

    private function getTeamWorkingTime($teamId, $date)
    {
        $workingTime = InspectorMission::with('workingTime')
            ->where('group_team_id', $teamId)
            ->whereDate('date', $date)
            ->where('day_off', 0)
            ->first();

        return $workingTime ? [
            'start_time' => $workingTime->workingTime->start_time,
            'end_time' => $workingTime->workingTime->end_time,
        ] : ['start_time' => null, 'end_time' => null];
    }
    private function assignPointsToTeam($groupId, $teamId, $date, $points)
    {
        $updatedMissions = InspectorMission::where('group_id', $groupId)
            ->where('group_team_id', $teamId)
            ->whereDate('date', $date)
            ->where('day_off', 0)
            ->pluck('id')
            ->toArray();

        // Get a list of points that are already assigned to any mission on this date
        $alreadyAssignedPoints = InspectorMission::whereDate('date', $date)
            ->whereIn('ids_group_point', $points)
            ->pluck('ids_group_point')
            ->flatten()
            ->unique()
            ->toArray();

        // Filter out the points that have already been assigned
        $pointsToAssign = array_diff($points, $alreadyAssignedPoints);

        // Assign only the available points to the team
        foreach ($updatedMissions as $updatedMission) {
            $updated = InspectorMission::where('id', $updatedMission)->where('vacation_id', null)->first();
            if ($updated && !empty($pointsToAssign)) {
                $updated->ids_group_point = array_map('strval', $pointsToAssign);
                $updated->save();
            }
        }
    }
}
