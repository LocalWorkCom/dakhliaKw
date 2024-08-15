<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\Grouppoint;
use Illuminate\Http\Request;
use App\Models\instantmission;
use App\Models\InspectorMission;
use App\Models\Inspector;

use Illuminate\Support\Facades\Auth;

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
           // dd($mission);
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
                        'point_name' => $groupPoint->name,
                        ' point_governate' => $groupPoint->government->name, // Assuming government name is the location
                        'point_time' => $inspectionTime, // Assuming 'time' is the attribute for time
                    ];
                }
                
            }
            // dd($groupPointsData);
            $missionData[] = [
                'mission_id' => $mission->id,
                'date' => $mission->date,
                'count' =>  $missionCount ,
                'instant_mission_count' =>  $instantmissioncount ,

                'points_count' =>  $groupPointCount ,
                'missions' => $groupPointsData,
            ];
        }

        // Include the instant missions in the response
        $instantMissionData = [];

        foreach ($instantmissions as $instantmission) {
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

        $responseData = [
            'missions' => $missionData,
            'instant_missions' => $instantMissionData,
        ];
        // $success['ViolationType'] = $missionData->map(function ($item) {
        //     return $item->only(['id', 'name']);
        // });
        // return response()->json($missionData);
        return $this->respondSuccess($responseData, 'Get Data successfully.');
    }

    
}
