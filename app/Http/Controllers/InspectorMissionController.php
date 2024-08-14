<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\Grouppoint;
use Illuminate\Http\Request;
use App\Models\instantmission;
use App\Models\InspectorMission;

use Illuminate\Support\Facades\Auth;

class InspectorMissionController extends Controller
{
    public function getMissionsByInspector()
    {
        $today = Carbon::today()->format('Y-m-d');
        // Retrieve the missions for the specific inspector
        // $missions = InspectorMission::whereDate('date', $today)->where('inspector_id', $inspectorId)->get();

         // Retrieve the currently authenticated user
         $inspectorId = Auth::id();

         // Retrieve the missions for the specific inspector
         $missions = InspectorMission::whereDate('date', $today)
             ->where('inspector_id', $inspectorId)
             ->with('workingTime', 'groupPoints.government')
             ->get();

             $instantmissions = instantmission::where('inspector_id', $inspectorId)
             ->get();
             
             $instantmissioncount = $instantmissions->count();
        $missionCount = $missions->count();
        
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

    $missionData = [];

    foreach ($missions as $mission) {
        $idsGroupPoint = is_array($mission->ids_group_point) ? $mission->ids_group_point : explode(',', $mission->ids_group_point);

          // Count the number of group points
          $groupPointCount = count($idsGroupPoint);
          
        // dd($groupPointCount);
        $groupPointsData = [];

        foreach ($idsGroupPoint as $groupId) {
            
            $groupPoint = Grouppoint::with('government')->find($groupId);
            
            if ($groupPoint) {
                
                // Fetch and format the working time
            $workingTime = $mission->workingTime;

            if ($workingTime) {
                $inspectionTime = "من {$workingTime->start_time} " . ($workingTime->start_time > 12 ? 'مساءا' : 'صباحا') . " الى {$workingTime->end_time} " . ($workingTime->end_time > 12 ? 'مساءا' : 'صباحا');
            } else {
                $inspectionTime = null; // Handle cases where working time is not found
            }
            
                $groupPointsData[] = [
                    'اسم النقطة' => $groupPoint->name,
                    'مكان التفتيش' => $groupPoint->government->name, // Assuming government name is the location
                    'وقت تفتيش النقطة' => $inspectionTime, // Assuming 'time' is the attribute for time
                ];
            }
            
        }
        // dd($groupPointsData);
        $missionData[] = [
            'mission_id' => $mission->id,
            'تاريخ اليوم' => $mission->date,
            'عدد المهام' =>  $missionCount ,
            'عدد الاوامر الفورية' =>  $instantmissioncount ,

            'عدد النقاط' =>  $groupPointCount ,
            'المهام' => $groupPointsData,
        ];
    }

     // Include the instant missions in the response
     $instantMissionData = [];

     foreach ($instantmissions as $instantmission) {
         $instantMissionData[] = [
             'instant_mission_id' => $instantmission->id,
             'اسم الامر الفوري' => $instantmission->label,  // Assuming description field
             'الموقع' => $instantmission->location,  
             'الوصف' => $instantmission->description,  
             'المجموعة' => $instantmission->group ? $instantmission->group->name : 'N/A',  // Include group name
             'الفريق' => $instantmission->groupTeam ? $instantmission->groupTeam->name : 'N/A',  // Include group team name ,
             'تاريخ الامر الفوري' => $instantmission->created_at->format('Y-m-d'),
         ];
     }

     $responseData = [
         'التفتيشات' => $missionData,
         'الاوامر الفورية' => $instantMissionData,
     ];
    // $success['ViolationType'] = $missionData->map(function ($item) {
    //     return $item->only(['id', 'name']);
    // });
    // return response()->json($missionData);
    return $this->respondSuccess($responseData, 'Get Data successfully.');
    }

    
}
