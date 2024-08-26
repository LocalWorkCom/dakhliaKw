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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InspectorMissionController extends Controller
{
    public function getMissionsByInspector()
    {
        $today = Carbon::today()->format('Y-m-d');
        // Retrieve the missions for the specific inspector
        // $missions = InspectorMission::whereDate('date', $today)->where('inspector_id', $inspectorId)->get();

         // Retrieve the currently authenticated user
         $inspector = Inspector::where('user_id',Auth::id())->first();
         // dd($inspectorId);
         $inspectorId=$inspector->id;

         // Retrieve the missions for the specific inspector
         $missions = InspectorMission::whereDate('date', $today)
             ->where('inspector_id', $inspectorId)
            // ->with('workingTime', 'groupPoints.government')
             ->get();
          // dd($missions[0]->ids_instant_mission);
           // $instantmissions = instantmission::where('inspector_id', $inspectorId)
           //  ->get();
        //   $instantmissions =$missions->ids_instant_mission;
             $instantmissioncount = 0;//$instantmissions->count();
        $missionCount = 0;//$missions->count();
        
        // // Group the missions by some criteria (e.g., date or group type)
        // $groupedMissions = $missions->groupBy('group_id'); // or another appropriate attribute

        // $missionGroups = [];

        // foreach ($groupedMissions as $groupId => $missions) {
        //     $missionGroup = [
        //         'group_name' => 'Group Name', // Get the group name by $groupId
        //         'date' => $missions->first()->date, // Assuming all missions in the group have the same date
        //         'mission_count' => $missions->count(),
        //         'missions' => $missions->map(function ($mission) {
        //             return [
        //                 'name' => $mission->group->name ?? 'Unknown', // Replace with appropriate field
        //                 'inspection_time' => 'من 8 صباحا الى 4 عصرا', // Replace with actual data
        //                 'inspection_location' => $mission->group->location ?? 'Unknown' // Replace with appropriate field
        //             ];
        //         })->toArray(),
        //     ];

            //     $missionGroups[] = $missionGroup;
            // }

        // return response()->json(['mission_groups' => $missionGroups]);
        $count=0;
        $missionData = [];

        foreach ($missions as $mission) {
            $idsGroupPoint = is_array($mission->ids_group_point) ? $mission->ids_group_point : explode(',', $mission->ids_group_point);
            if (!is_null($mission->ids_instant_mission)) {
                // The variable is null
                $instantMissions= is_array($mission->ids_instant_mission) ? $mission->ids_instant_mission : explode(',', $mission->ids_instant_mission);
            }else {
                $instantMissions=$mission->ids_instant_mission;
            }
        
          //  dd($mission->ids_instant_mission);
            // Count the number of group points
            $groupPointCount = count($idsGroupPoint);
            $count+=$groupPointCount;
            // dd($groupPointCount);
           /**
            * Normal mission assigned to inspectors
            */

            foreach ($idsGroupPoint as $groupId) {
                $groupPointsData = [];
                $groupPoint = Grouppoint::with('government')->find($groupId);
              //  $groupPoint = Grouppoint::with('government')->find($groupId);
                //dd($groupPoint);
                if ($groupPoint) {
                    $idsPoints = is_array($groupPoint->points_ids) ? $groupPoint->points_ids : explode(',', $groupPoint->points_ids);
              //   $groupPointCount=count($idsPoints);
                
                    foreach($idsPoints as $pointId)
                    {
                        $point = Point::with('government')->find($pointId);
                       // dd($point);
                        if ($point->work_type==1) {
                            $today=date('w');
                            $workTime=PointDays::where('point_id',$pointId)->where('name',$today)->first();
                            $startTime= Carbon::create(date('y-m-d').' '.$workTime->from );
                            $endtTime= Carbon::create(date('y-m-d').' '.$workTime->to );
                            $fromTime=$startTime->format('h:i');
                            $ToTime= $endtTime->format('h:i');
                        $inspectionTime = "من {$fromTime} " . ($workTime->from > 12 ? 'مساءا' : 'صباحا') . " الى {$ToTime} " . ($workTime->to > 12 ? 'مساءا' : 'صباحا');
                    } else {
                        $inspectionTime = 'طول اليوم'; // Handle cases where working time is not found
                    } 

            
                      
                $groupPointsData[] = [
                    'point_id' => $point->id,
                    'point_name' => $point->name,
                    'point_governate' => $point->government->name, // Assuming government name is the location
                    'point_time' => $inspectionTime, // Assuming 'time' is the attribute for time
                    'point_location' => $point->google_map, // Assuming 'time' is the attribute for time
                    'latitude'=> $point->lat, 
                    'longitude'=> $point->long, 

                        ];
                    }
                    // Fetch and format the working time
               // $workingTime = $mission->workingTime;
               $missionData[] = [
                'mission_id' => $mission->id,
                'governate' => $groupPoint->government->name,
                'name' =>  $groupPoint->name ,
                'points_count' =>  $groupPointCount ,
                'points' => $groupPointsData,
                
                 ];
            
                }
                
            }
            // dd($groupPointsData);
            /**
             * Instant Mission Data
             */
            //dd($instantMissions);
        //    $instantmissioncount=count($instantMissions);
          //  dd($instantmissioncount);
           
            $instantMissionData = [];
            if (!is_null($mission->ids_instant_mission)) {
             foreach ($instantMissions as $instant) {
                $instantmissioncount++;
                $instantmission =  instantmission::find($instant);
                //dd( $instabtMi);
                
                if($instantmission)
              {  $instantMissionData[] = [

                    'instant_mission_id' => $instantmission->id,
                    'name' => $instantmission->label,  // Assuming description field
                    'location' => $instantmission->location,  
                    'description' => $instantmission->description,  
                    'group' => $instantmission->group ? $instantmission->group->name : 'N/A',  // Include group name
                    'team' => $instantmission->groupTeam ? $instantmission->groupTeam->name : 'N/A',  // Include group team name ,
                    'date' => $instantmission->created_at->format('Y-m-d'),
                   
                    'latitude'=> $instantmission->latitude, 
                    'longitude'=> $instantmission->longitude, 
                ];}
             }
            }
        }
        $count+=$instantmissioncount;

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
            'date'=>date('Y-m-d'),
            'mission_count'=>$count,
            'instant_mission_count' => $instantmissioncount,
            'groupPointCount'=>$groupPointCount,
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
        $inspector = Inspector::where('user_id',Auth::id())->first();
      // $inspector=Auth::user()->inspectorId;
        //dd($inspector);
        $todayMission=InspectorMission::with('workingTime','workingTree')->where('date',date('Y-m-d'))->where('inspector_id',$inspector->id)->first();
      /*   if($todayMission->day_off==1)
        {
            $success['dayOff'] = 1;
            return $this->respondSuccess(json_decode('{"dayOff":1}'), 'يوم راحة لايوجد دوام');

        }else{ */
            $success['dayOff'] = 0;  
            $success['name'] =$todayMission->workingTree->name ; 
            $success['workdays'] =$todayMission->workingTree->working_days_num ; 
            $success['holidaydays'] =$todayMission->workingTree->holiday_days_num ; 
            $success['todayTimes_start'] =$todayMission->workingTime->start_time ; 
            $success['todayTimes_end'] =$todayMission->workingTime->end_time ; 

            return $this->respondSuccess($success, 'بيانات اللازم اليوم');



       // }
    }
    
}