<?php

namespace App\Http\Controllers\Api;

use App\Models\PersonalMission;
use Carbon\Carbon;

use App\Models\Grouppoint;
use Illuminate\Http\Request;
use App\Models\instantmission;
use App\Models\InspectorMission;
use App\Models\Inspector;
use App\Models\Point;
use App\Models\PointDays;
use App\Http\Controllers\Controller;
use App\Models\GroupTeam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        if ($team_time->working_time_id->isNotEmpty()) {
            // Assuming you want to access the first item
            $startTimeofTeam = $team_time->first()->workingTime->start_time;
            $endTimeofTeam = $team_time->first()->workingTime->start_time;

        }else{
            $responseData = [
                'date' => date('Y-m-d'),
                'mission_count' => null,
                'instant_mission_count' =>  null,
                'groupPointCount' => null,
                'missions' => null,

                'instant_missions' => null,
            ];
            // $success['ViolationType'] = $missionData->map(function ($item) {
            //     return $item->only(['id', 'name']);
            // });
            // return response()->json($missionData);
            return $this->respondSuccess($responseData, 'Get Data successfully.');
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
        $avilable=true;
        $groupPointCount =0;
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
                $groupPoint = Grouppoint::with('government')->find($groupId);
                //  $groupPoint = Grouppoint::with('government')->find($groupId);
                //dd($groupPoint);
                if ($groupPoint) {
                    $idsPoints = is_array($groupPoint->points_ids) ? $groupPoint->points_ids : explode(',', $groupPoint->points_ids);
                    //   $groupPointCount=count($idsPoints);

                    foreach ($idsPoints as $pointId) {
                        $point = Point::with('government')->find($pointId);
                       // dd($point);
                        if ($point->work_type == 1) {
                            $today = date('w');
                            $workTime = PointDays::where('point_id', $pointId)->where('name', $today)->first();
                           //c  dd($workTime);
                            $startTime = Carbon::create(date('y-m-d') . ' ' . $workTime->from);
                            $endtTime = Carbon::create(date('y-m-d') . ' ' . $workTime->to);
                            $fromTime = $startTime->format('H:i');
                            $ToTime = $endtTime->format('H:i');
                            $pointTime=[
                                'startTime '=> $workTime->from,
                                'endTime ' => $workTime->to
                            ];
                            $inspectionTime = "من {$fromTime} " . ($workTime->from > 12 ? 'مساءا' : 'صباحا') . " الى {$ToTime} " . ($workTime->to > 12 ? 'مساءا' : 'صباحا');
                            $is_avilable = $this->isTimeAvailable($fromTime, $ToTime);
                            if($is_avilable){
                                $avilable= true;
                            }else{
                                $avilable= false;
                            }
                        } else {$pointTime = [
                                'startTime' => '00:00',  // Full day start time
                                'endTime' => '23:59'     // Full day end time
                            ];
                            $inspectionTime = 'طول اليوم'; // Handle cases where working time is not found
                            $avilable= true;
                        }



                        $groupPointsData[] = [
                            'point_id' => $point->id,
                            'point_name' => $point->name,
                            'point_governate' => $point->government->name, // Assuming government name is the location
                            'point_time' => $inspectionTime, // Assuming 'time' is the attribute for time
                            'point_shift' => $pointTime,
                            'point_location' => $point->google_map, // Assuming 'time' is the attribute for time
                            'Point_availability'=>$avilable,
                            'latitude' => $point->lat,
                            'longitude' => $point->long,

                        ];
                    }
                    // Fetch and format the working time
                    // $workingTime = $mission->workingTime;
                    $missionData[] = [
                        'mission_id' => $mission->id,
                        'governate' => $groupPoint->government->name,
                        'name' =>  $groupPoint->name,
                        'points_count' =>  $groupPointCount,
                        'points' => $groupPointsData,

                    ];
                }
            }
            // dd($groupPointsData);

            //dd($instantMissions);
            //    $instantmissioncount=count($instantMissions);
            //  dd($instantmissioncount);

            $instantMissionData = [];
            if (!is_null($mission->ids_instant_mission)) {
                foreach ($instantMissions as $instant) {
                    $instantmissioncount++;
                    $instantmission =  instantmission::find($instant);
                    //dd( $instabtMi);

                    if ($instantmission) {

                        if (str_contains($instantmission->location, 'gis.paci.gov.kw')) {
                            // dd("yes");
                            $location = null;
                            $kwFinder = $instantmission->location;
                        } else {
                            $location = $instantmission->location;
                            $kwFinder = null;
                        }


                        $instantMissionData[] = [

                            'instant_mission_id' => $instantmission->id,
                            'name' => $instantmission->label,  // Assuming description field
                            // 'location' => $instantmission->location,
                            'location' => $location,
                            'KWfinder' => $kwFinder,
                            'description' => $instantmission->description,
                            'group' => $instantmission->group ? $instantmission->group->name : 'N/A',  // Include group name
                            'team' => $instantmission->groupTeam ? $instantmission->groupTeam->name : 'N/A',  // Include group team name ,
                            'date' => $instantmission->created_at->format('Y-m-d'),

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
        $responseData = [
            'date' => date('Y-m-d'),
            'mission_count' => $count,
            'instant_mission_count' => $instantmissioncount,
            'groupPointCount' => $groupPointCount,
            'missions' => $missionData,

            'instant_missions' => $instantMissionData,
        ];
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
      //  dd($todayMission);
      if( ($todayMission->workingTree)->isNotEmpty()){
        $success['dayOff'] = 0;
        $success['name'] = $todayMission->workingTree->name;
        $success['workdays'] = $todayMission->workingTree->working_days_num;
        $success['holidaydays'] = $todayMission->workingTree->holiday_days_num;
        $success['todayTimes_start'] = $todayMission->workingTime->start_time;
        $success['todayTimes_end'] = $todayMission->workingTime->end_time;
      }else{
        $success['dayOff'] = 0;
        $success['name'] = null;
        $success['workdays'] = null;
        $success['holidaydays'] = null;
        $success['todayTimes_start'] =null;
        $success['todayTimes_end'] = null;
      }


        return $this->respondSuccess($success, 'بيانات اللازم اليوم');



        // }
    }
}
