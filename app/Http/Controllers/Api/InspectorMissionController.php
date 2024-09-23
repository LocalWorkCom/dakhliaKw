<?php

namespace App\Http\Controllers\Api;

use App\Models\PersonalMission;
use App\Models\Violation;
use Carbon\Carbon;

use App\Models\Grouppoint;
use Illuminate\Http\Request;
use App\Models\instantmission;
use App\Models\InspectorMission;
use App\Models\Inspector;
use App\Models\Point;
use App\Models\PointDays;
use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\GroupTeam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use IntlDateFormatter;

class InspectorMissionController extends Controller
{
    function isTimeAvailable($pointStart, $pointEnd)
    {
        $currentTime = Carbon::now()->format('H:i');

        // Convert the times to Carbon instances for easy comparison
        $start = Carbon::createFromTimeString($pointStart);
        $end = Carbon::createFromTimeString($pointEnd)->addMinutes(30);
        $current = Carbon::createFromTimeString($currentTime);

        return $current->between($start, $end);
    }
    public function getMissionsByInspector()
    {
        $today = Carbon::today()->format('Y-m-d');
        // Retrieve the currently authenticated user
        $inspector = Inspector::where('user_id', Auth::id())->first();
        // dd($inspectorId);
        $inspectorId = $inspector->id;
        $team_time = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->with('workingTime')
            ->get();
        // Check if the collection has any items
        if ($team_time->isNotEmpty() && $team_time->first()->day_off != 1) {

            // $startTimeofTeam = $team_time->first()->workingTime->start_time;
            // $endTimeofTeam = $team_time->first()->workingTime->end_time;
            // $name = $team_time->first()->workingTime->name;
            $inspector_shift = [
                'name' => $team_time->first()->workingTime->name,
                'start_time' => $team_time->first()->workingTime->start_time,
                'end_time' => $team_time->first()->workingTime->end_time
            ];
        }
        // else{

        // }
        // Retrieve the missions for the specific inspector
        $missions = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            // ->with('workingTime', 'groupPoints.government')
            ->get();
        $instantmissioncount = 0; //$instantmissions->count();
        $missionCount = 0; //$missions->count();

        $count = 0;
        $missionData = [];
        $avilable = true;
        $groupPointCount = 0;
        $instantMissionData = [];
        foreach ($missions as $mission) {
            $idsGroupPoint = is_array($mission->ids_group_point) ? $mission->ids_group_point : explode(',', $mission->ids_group_point);
            if (!is_null($mission->ids_instant_mission)) {
                // The variable is null
                $instantMissions = is_array($mission->ids_instant_mission) ? $mission->ids_instant_mission : explode(',', $mission->ids_instant_mission);
            } else {
                $instantMissions = $mission->ids_instant_mission;
            }
            $groupPointCount = count($idsGroupPoint);
            $count += $groupPointCount;
            foreach ($idsGroupPoint as $groupId) {
                $groupPointsData = [];
                $groupPoint = Grouppoint::with('government', 'sector')->find($groupId);
                if ($groupPoint) {
                    $idsPoints = is_array($groupPoint->points_ids) ? $groupPoint->points_ids : explode(',', $groupPoint->points_ids);
                    $groupPointsData = [];
                    foreach ($idsPoints as $pointId) {
                        $point = Point::with('government')->find($pointId);
                        if ($point) {
                            $today = date('w');
                            $inspectionTime = '';
                            $avilable = false;
                            $pointTime = ['startTime' => '00:00', 'endTime' => '23:59']; // Default to full day

                            if ($point->work_type == 1) {
                                $workTime = PointDays::where('point_id', $pointId)->where('name', $today)->first();
                                if ($workTime) {
                                    $startTime = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' ' . $workTime->from);
                                    $endtTime = Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' ' . $workTime->to);
                                    $fromTime = $startTime->format('H:i');
                                    $toTime = $endtTime->format('H:i');

                                    $inspectionTime = "من {$fromTime} " . ($workTime->from > 12 ? 'مساءا' : 'صباحا') . " الى {$toTime} " . ($workTime->to > 12 ? 'مساءا' : 'صباحا');
                                    $avilable = $this->isTimeAvailable($fromTime, $toTime);

                                    $pointTime = ['startTime' => $fromTime, 'endTime' => $toTime];
                                } else {
                                    // If working time is not found, default to full day
                                    $inspectionTime = 'طول اليوم';
                                    $avilable = true;
                                }
                            } else {
                                $inspectionTime = 'طول اليوم';
                                $avilable = true;
                            }

                            $date = Carbon::today()->format('Y-m-d');
                            $violationCount = Violation::where('point_id', $point->id)->where('status', 1)->whereDate('created_at', $date)->count();
                            $absenceCount = Absence::where('point_id', $point->id)->where('flag', 1)->whereDate('date', $date)->count();
                            $is_visited = ($violationCount > 0 || $absenceCount > 0);
                            $sector = $point->sector->name;
                            $groupPointsData[] = [
                                'point_id' => $point->id,
                                'point_name' => $point->name,
                                'point_governate' => $point->government->name,
                                'point_time' => $inspectionTime,
                                'point_shift' => $pointTime,
                                'point_location' => $point->google_map,
                                'Point_availability' => $avilable,
                                'latitude' => $point->lat,
                                'longitude' => $point->long,
                                'is_visited' => $is_visited,
                                'count_violation' => $violationCount,
                                'count_absence' => $absenceCount
                            ];
                        }
                        // dd('k');
                    }


                    $missionData[] = [
                        'mission_id' => $mission->id,
                        'inspector_shift' => $inspector_shift,
                        'governate' => $groupPoint->government->name,
                        'sector' => $sector,
                        'name' => $groupPoint->name,
                        'points_count' => count($groupPointsData),
                        'points' => $groupPointsData,
                        'created_at' => $mission->created_at
                    ];
                }
            }
            $instantMissionData = [];
            if (!is_null($mission->ids_instant_mission)) {
                foreach ($instantMissions as $instant) {
                    $instantmissioncount++;
                    $instantmission =  instantmission::find($instant);
                    // dd( $instantmission);

                    if ($instantmission) {

                        if (str_contains($instantmission->location, 'gis.paci.gov.kw')) {
                            // dd("yes");
                            $location = null;
                            $kwFinder = $instantmission->location;
                        } else {
                            $location = $instantmission->location;
                            $kwFinder = null;
                        }


                        $createdAt = $instantmission->created_at;



                        $time = $createdAt->format('h:i'); // 12-hour format
                        $time_arabic = str_replace(['AM', 'PM'], ['صباحا', 'مساءا'], $time);

                        $instantMissionData[] = [
                            'instant_mission_id' => $instantmission->id,
                            'name' => $instantmission->label,
                            'location' => $location,
                            'KWfinder' => $kwFinder,
                            'description' => $instantmission->description,
                            'group' => $instantmission->group ? $instantmission->group->name : 'N/A',  // Include group name
                            'team' => $instantmission->groupTeam ? $instantmission->groupTeam->name : 'N/A',  // Include group team name
                            'date' => $createdAt->format('Y-m-d'),
                            'time' => $time?? null,
                            'time_name' => $time_arabic?? null,
                            'latitude' => $instantmission->latitude,
                            'longitude' => $instantmission->longitude,
                        ];
                    }
                }
            }
        }
        $count += $instantmissioncount;

        // Include the instant missions in the response
        //   $instantMissionData = [];

        /*  foreach ($instantmissions as $instantmission) {
            $instantMissionData[] = [
                'instant_mission_id' => $instantmission->id,
                'name' => $instantmission->label,  // Assuming description field
                'location' => $instantmission->location,
                'description' => $instantmission->description,
                'group' => $instantmission->group ? $instantmission->group->name : 'N/A',  // Include group name
                'team' => $instantmission->groupTeam ? $instantmission->groupTeam->name : 'N/A',  // Include group team name ,
                'date' => $instantmission->created_at->format('Y-m-d'),
            ];
        }
        */
        $dayNamesArabic = [
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
            'Saturday'  => 'السبت',
        ];
        $dayName = date('l');
        $date = date('Y-m-d');
        if ($missionData) {
            $responseData = [
                'date' => $date,
                'date_name' => $dayNamesArabic[$dayName],
                'mission_count' => $count,
                'instant_mission_count' => $instantmissioncount,
                'groupPointCount' => $groupPointCount,
                'missions' => $missionData,
                'instant_missions' => $instantMissionData,
            ];
        } else {
            $responseData = [
                'date' => $dayNamesArabic[$dayName] . ', ' . $date,
                'mission_count' => 0,
                'instant_mission_count' => $instantmissioncount,
                'groupPointCount' => 0,
                'missions' => null,
                'instant_missions' => $instantMissionData,
            ];
        }

        // $success['ViolationType'] = $missionData->map(function ($item) {
        //     return $item->only(['id', 'name']);
        // });
        // return response()->json($missionData);
        return $this->respondSuccess($responseData, 'Get Data successfully.');
    }


    /**
     * Lizamat
     */
    public function get_shift(Request $request)
    {
        $inspector = Inspector::where('user_id', Auth::id())->first();
        // $inspector=Auth::user()->inspectorId;
        //dd($inspector);
        $todayMission = InspectorMission::with('workingTime', 'workingTree')->where('date', date('Y-m-d'))->where('inspector_id', $inspector->id)->first();
        /*   if($todayMission->day_off==1)
        {
            $success['dayOff'] = 1;
            return $this->respondSuccess(json_decode('{"dayOff":1}'), 'يوم راحة لايوجد دوام');

        }else{ */

        if ($todayMission->day_off == 0) {
            $success['dayOff'] = 0;
            $success['name'] = $todayMission->workingTree->name;
            $success['workdays'] = $todayMission->workingTree->working_days_num;
            $success['holidaydays'] = $todayMission->workingTree->holiday_days_num;
            $success['todayTimes_start'] = $todayMission->workingTime->start_time;
            $success['todayTimes_end'] = $todayMission->workingTime->end_time;
        } else {
            $success['dayOff'] = 0;
            $success['name'] = null;
            $success['workdays'] = null;
            $success['holidaydays'] = null;
            $success['todayTimes_start'] = null;
            $success['todayTimes_end'] = null;
        }


        return $this->respondSuccess($success, 'بيانات اللازم اليوم');



        // }
    }
}
