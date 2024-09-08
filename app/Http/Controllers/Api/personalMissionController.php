<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\PersonalMission;
use App\Models\Point;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class personalMissionController extends Controller
{
    public function getAllPoints(Request $request)
    {

        $today = Carbon::today()->toDateString();
        $daysOfWeek = [
           
            "الأحد",  // 0
            "الإثنين",  // 1
            "الثلاثاء",  // 2
            "الأربعاء",  // 3
            "الخميس",  // 4
            "الجمعة",  // 5
            "السبت",  // 6
        ];
        $dayWeek = Carbon::now()->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);
        
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $inspector = GroupTeam::with('group')->whereJsonContains('inspector_ids', $inspectorId)->first();
        
        if ($inspector) {
            $inspector_points = InspectorMission::where('inspector_id', $inspectorId)
                ->where('date', $today)
                ->pluck('ids_group_point')
                ->map(function ($json) {
                    return is_string($json) ? json_decode($json, true) : $json;
                })
                ->flatten()
                ->filter()
                ->toArray();
        
            // Fetch available group points
            $availablegroup_points = Grouppoint::where('government_id', $inspector->group->government_id)
                ->whereNotIn('id', $inspector_points)
                ->get();
        
            $All_points = []; 
        
            // Process available group points
            $availablegroup_points->each(function ($grouppoint) use (&$All_points, $daysOfWeek, $index, $dayWeek) {
                // Fetch points related to the group point
                $available_points = Point::with(['pointDays'])->whereIn('id', $grouppoint->points_ids)->get();
                $name= $grouppoint->flag == 0 ? 'لا توجد مجموعه': $grouppoint->name;
        
                $available_points->each(function ($available_point) use (&$All_points, $daysOfWeek, $index, $dayWeek,$name) {
                    if ($available_point->work_type == 0) {
                        // Check if today's day is in days_work
                        $is_off = in_array($index, $available_point->days_work);
                        
                        if ($is_off) {
                            $All_points[] = [
                                'point_governate' => $available_point->government->name , 
                                'point_id' => $available_point->id,
                                'point_name' => $available_point->name,
                                'point_GroupName' => $name ?? 'Unknown',
                                'point_time' => 'طوال اليوم',
                                'point_location' => $available_point->google_map,
                            ];
                        }
                    } else {
                        $pointDay =  $available_point->pointDays->where('name',$index)->first();
                        $All_points[] = [
                            'point_governate' => $available_point->government->name , 
                            'point_id' => $available_point->id,
                            'point_name' => $available_point->name,
                            'point_GroupName' => $name ?? 'Unknown',
                            'point_time' => "من {$pointDay->from} " . ($pointDay->from > 12 ? 'مساءا' : 'صباحا') . " الى {$pointDay->to} " . ($pointDay->to > 12 ? 'مساءا' : 'صباحا'),
    
                            'point_location' => $available_point->google_map,
                        ];
                    }
                });
            });
        
            $success['available_points'] = $All_points;
            return $this->respondSuccess($success, 'Get Data successfully.');
        } else {
            return $this->respondError('type not found', ['error' => 'خطأ فى استرجاع البيانات'], 404);
        }
        
    }
    public function addPersonalMission(Request $request)
    {


        $messages = [
            'pointID.required' => 'يجب اختيار النقطه المضاف لها المهمه',
            'pointID.exists' => 'عفوا هذه النقطه غير متاحه',
        ];
        $validatedData = Validator::make($request->all(), [
            'pointID' => ['required', function ($attribute, $value, $fail) {
                $exists = Grouppoint::whereJsonContains('points_ids', (string) $value)->exists();
                if (!$exists) {
                    $fail('عفوا هذه النقطه غير متاحه');
                }
            }],
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->toDateString();
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $inspector = GroupTeam::with('group')->whereJsonContains('inspector_ids', $inspectorId)->first();
        $point_group = Grouppoint::whereJsonContains('points_ids', $request->pointID)->value('id');
        $is_added_before = PersonalMission::where('point_id', $point_group)->where('inspector_id', $inspectorId)->where('date', $today)->get();
        if (!($is_added_before->isEmpty())) {

            return $this->respondError('failed to save', ['error' => 'عفوا تمت أضافه هذه المهمه من قبل لك'], 404);
        }
        $new = new PersonalMission();
        $new->date = $today;
        $new->inspector_id = $inspectorId;
        $new->point_id  = $point_group;
        $new->group_id  = $inspector->group_id;
        $new->team_id  = $inspector->id;
        $new->save();
        $update_mission = InspectorMission::where('inspector_id', $inspectorId)->where('date', $today)->first();
        $currentArray = $update_mission->personal_mission_ids ? json_decode($update_mission->personal_mission_ids, true) : [];
        $currentArray[] = $new->id;
        $update_mission->personal_mission_ids = json_encode($currentArray);
        $update_mission->save();

        //send notification to inspector 

        if ($new) {
            return $this->respondSuccess('success', 'Data Saved successfully.');
        } else {
            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
        }
    }
    // foreach ($available_points as $available_point) {
    //     // Debugging to check if 'grouppoint' relationship is loaded and 'flag' exists
    //     dd($available_point->grouppoint->flag);

    //     if ($available_point->work_type == 0) {
    //         // Check if today's day is in days_work
    //         $is_off = in_array($index, $available_point->days_work);

    //         if ($is_off) {
    //             $All_points[] = [
    //                 'pointId' => $available_point->id,
    //                 'pointName' => $available_point->name,
    //                 'pointGroupName' => $available_point->grouppoint->name ?? 'Unknown', // Ensure this is the correct field
    //                 'pointgovernment_name' => $available_point->government->name,
    //                 'work_type' => 'full Time',
    //                 'point_work_days' => array_map(function ($dayIndex) use ($daysOfWeek, $is_off) {
    //                     $index = intval($dayIndex); // Convert to integer to get the index
    //                     return [
    //                         'name' => isset($daysOfWeek[$index]) ? $daysOfWeek[$index] : 'Unknown',
    //                         'is_thisDay_off' => $is_off,
    //                     ];
    //                 }, $available_point->days_work),
    //             ];
    //         }
    //     } else {
    //         // Assuming 'part Time' for work_type == 1
    //         $All_points[] = [
    //             'pointId' => $available_point->id,
    //             'pointName' => $available_point->name,
    //             'pointGroupName' => $available_point->grouppoint->name ?? 'Unknown', // Ensure this is the correct field
    //             'pointgovernment_name' => $available_point->government->name,
    //             'work_type' => 'part Time',
    //             'point_work_days' => [
    //                 'dayname' => $available_point->pointDays->map(function ($pointDay) use ($daysOfWeek, $dayWeek) {
    //                     $index = intval($pointDay->name); // Convert to integer to get the index
    //                     return [
    //                         'is_thisDay_off' => $pointDay->name == $dayWeek ? false : true,
    //                         'name' => isset($daysOfWeek[$index]) ? $daysOfWeek[$index] : $pointDay->name,
    //                         'from' => $pointDay->from ?? '',
    //                         'to' => $pointDay->to ?? '',
    //                     ];
    //                 })->toArray(), // Convert the collection to an array
    //             ],
    //         ];
    //     }
    // }
}
