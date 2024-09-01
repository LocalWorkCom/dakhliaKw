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

class governmentController extends Controller
{
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

    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        while ($startOfMonth->lte($endOfMonth)) {
            // Define dates for this iteration
            $today = $startOfMonth->toDateString();
            $yesterday = $startOfMonth->copy()->subDay()->toDateString();
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

        $allSectors = Sector::get();
        $groupGovernmentIds = [];
        $ids_grouppoints = [];
        foreach ($allSectors as $sector) {
            $allAvailablePoints = Grouppoint::where('sector_id', $sector->id)
                ->where('deleted', 0)
                ->select('government_id', 'id', 'points_ids')
                ->get();

            foreach ($allAvailablePoints as $grouppoint) {
                $available_points = Point::with('pointDays')->whereIn('id', $grouppoint->points_ids)->get();

                foreach ($available_points as $available_point) {
                    if ($available_point->work_type == 0) {
                        $is_off = in_array($index, $available_point->days_work);
                        if ($is_off) {
                            // Group points by government_id
                            if (!isset($groupGovernmentIds[$available_point->government_id])) {
                                $groupGovernmentIds[$available_point->government_id] = [];
                            }
                            $groupGovernmentIds[$available_point->government_id][] = [
                                'id' => $available_point->id,
                                'grouppoint_id' => $grouppoint->id,
                                'work_type' => 0,
                            ];
                            $ids_grouppoints[] = $grouppoint->id;
                        }
                    } else {
                        $pointDay = $available_point->pointDays->where('name', $index)->first();
                        if ($pointDay) {
                            // Group points by government_id
                            if (!isset($groupGovernmentIds[$available_point->government_id])) {
                                $groupGovernmentIds[$available_point->government_id] = [];
                            }
                            $groupGovernmentIds[$available_point->government_id][] = [
                                'id' => $available_point->id,
                                'grouppoint_id' => $grouppoint->id,
                                'work_type' => 1,
                                'point_time' => [$pointDay->from, $pointDay->to],
                            ];
                            $ids_grouppoints[] = $grouppoint->id;
                        }
                    }
                }
            }
            print_r($ids_grouppoints);
            //dd($groupGovernmentIds);
            $allGroupsForSector = Groups::where('sector_id', $sector->id)
                ->select('id', 'points_inspector')
                ->get();
            foreach ($allGroupsForSector as $groupID) {
                // dd($group->id);
                $teams = GroupTeam::where('group_id', $groupID->id)->pluck('id')->toArray();
                $groupTeams = InspectorMission::where('group_id', $groupID->id)->whereIn('group_team_id', $teams)
                    ->select('group_team_id', 'ids_group_point')
                    ->whereDate('date', $yesterday)
                    ->distinct('group_team_id')
                    ->get();

                if ($groupTeams->isEmpty()) {
                    $groupTeams = InspectorMission::where('group_id', $groupID->id)->whereIn('group_team_id', $teams)
                        ->select('group_team_id', 'ids_group_point')
                        ->whereDate('date', $today)
                        ->distinct('group_team_id')
                        ->get();
                }

                $teamPointsYesterday = [];
                $teamPointsToday = [];
                $dayOffTeams = [];
                // At this point, $validPoints should contain exactly $pointPerTeam points
                $availableGrouppointIds = [];
                foreach ($groupTeams as $groupTeam) {
                    $teamPointsYesterday[$groupTeam->group_team_id] = $groupTeam->ids_group_point ? $groupTeam->ids_group_point : [];
                    $pointPerTeam = $groupID->points_inspector;

                    $teamsWorkingTime = InspectorMission::with('workingTime')
                        ->where('group_id', $groupID->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 0)
                        ->distinct('group_team_id')
                        ->get();

                    $teamTimePeriods = $teamsWorkingTime->map(function ($mission) {
                        return [$mission->workingTime->start_time, $mission->workingTime->end_time];
                    })->toArray();

                    $teamsWithDayOff = InspectorMission::where('group_id', $groupID->id)
                        ->where('group_team_id', $groupTeam->group_team_id)
                        ->whereDate('date', $today)
                        ->where('day_off', 1)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($teamsWithDayOff)) {
                        $dayOffTeams = array_merge($dayOffTeams, $teamsWithDayOff);
                        continue;
                    }
                    // dd($teamPointsYesterday[$groupTeam->group_team_id]);

                    $availablePoints = array_diff($ids_grouppoints, $teamPointsYesterday[$groupTeam->group_team_id] ?? []);
                    print_r($availablePoints);
                    // $availablePoints = 
                    if (!empty($availablePoints)) {
                        foreach ($availablePoints as $pointsBygovernmnet) {
                            $validPoints = [];

                            // Continue fetching points until we have enough valid points
                            while (count($validPoints) < $pointPerTeam && !empty($pointsBygovernmnet)) {
                                // Get a chunk of points to process
                                $possibleNewValues = array_splice($availablePoints, 0, $pointPerTeam);

                                foreach ($possibleNewValues as $pointId) {
                                    foreach ($groupGovernmentIds as $groupGovernmentId) {
                                        foreach ($groupGovernmentId as $groupGovernmId) {
                                            if ($groupGovernmId['grouppoint_id'] == $pointId) {
                                                if ($groupGovernmId['work_type'] == 0) {
                                                    // No time range check needed, add directly
                                                    $validPoints[] = $pointId;
                                                    unset($groupGovernmId);
                                                } else {
                                                    $pointTimeRange = $groupGovernmId['point_time'] ?? [];

                                                    // Check time overlap
                                                    foreach ($teamTimePeriods as $teamTime) {
                                                        if ($this->overlaps($pointTimeRange, $teamTime)) {
                                                            $validPoints[] = $pointId;
                                                            unset($groupGovernmId);
                                                            break; // Move to next point
                                                        }
                                                    }
                                                }

                                                // If the group is now empty, remove the entire government entry
                                                if (empty($groupGovernmentId)) {
                                                    unset($groupGovernmentIds[$groupGovernmId]);
                                                }
                                            }
                                        }
                                    }

                                    // If we've gathered enough valid points, break out of the loop
                                    if (count($validPoints) >= $pointPerTeam) {
                                        break;
                                    }
                                    // Check if we managed to gather enough valid points
                                    if (count($validPoints) < $pointPerTeam) {
                                        // Not enough valid points, continue to next $pointsBygovernmnet
                                        continue;
                                    }
                                }
                            }


                            // dd($validPoints);

                            // // Proceed with valid points as before
                            // foreach ($validPoints as $pointId) {
                            //     foreach ($groupGovernmentIds as $governmentId => &$group) { // Use reference to modify the array
                            //         foreach ($group as $key => $item) {
                            //             if ($item['id'] === $pointId) {
                            //                 $grouppointId = $item['grouppoint_id'];
                            //                 $availableGrouppointIds[] = $grouppointId;

                            //                 // Remove the item from $groupGovernmentIds
                            //                 unset($group[$key]);
                            //                 // If the group is now empty, remove the entire government entry
                            //                 if (empty($group)) {
                            //                     unset($groupGovernmentIds[$governmentId]);
                            //                 }
                            //             }
                            //         }
                            //     }
                            // }

                            // Ensure unique grouppoint_ids
                            $availableGrouppointIds = array_unique($validPoints);
                            $teamPointsToday[$groupTeam->group_team_id] = $availableGrouppointIds;
                            // dd($groupID);
                            $updatedMissions = InspectorMission::where('group_id', $groupID->id)
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
                        }
                    }
                }
            }
        }
    }
}
