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
            "السبت",  // Saturday
            "الأحد",  // Sunday
            "الإثنين",  // Monday
            "الثلاثاء",  // Tuesday
            "الأربعاء",  // Wednesday
            "الخميس",  // Thursday
            "الجمعة",  // Friday
        ];
        $dayWeek = Carbon::now()->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);
        
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $inspector = GroupTeam::with('group')->whereJsonContains('inspector_ids', $inspectorId)->first();
        
        if ($inspector) {
            $inspector_points = InspectorMission::where('inspector_id', $inspectorId)
                ->where('date', $today)
                ->get()
                ->pluck('ids_group_point')
                ->map(function ($json) {
                    // Decode only if $json is a string
                    return is_string($json) ? json_decode($json, true) : $json;
                })
                ->flatten()
                ->filter()
                ->toArray();
        
            // Fetch available group points
            $availablegroup_points = Grouppoint::where('government_id', $inspector->group->government_id)
                ->whereNotIn('id', $inspector_points)
                ->pluck('points_ids')
                ->map(function ($json) {
                    return is_string($json) ? json_decode($json, true) : $json;
                })
                ->flatten()
                ->filter()
                ->toArray();
        
            $available_points = Point::with('pointDays')->whereIn('id', $availablegroup_points)->get();
            
            $All_points = []; // Initialize $All_points array
        
            foreach ($available_points as $available_point) {
                if ($available_point->work_type == 0) {
                    $is_off = in_array($dayWeek, $available_point->days_work);
                    
                    $All_points[] = [
                        'pointId' => $available_point->id,
                        'pointName' => $available_point->name,
                        'pointgovernment_name' => $available_point->government->name,
                        'work_type' => 'full Time',
                        'point_work_days' => array_map(function ($dayIndex) use ($daysOfWeek, $is_off) {
                            $index = intval($dayIndex); // Convert to integer to get the index
                            return [
                                'name' => isset($daysOfWeek[$index]) ? $daysOfWeek[$index] : 'Unknown',
                                'is_thisDay_off' => $is_off,
                            ];
                        }, $available_point->days_work),
                    ];
                } else {
                    $All_points[] = [
                        'pointId' => $available_point->id,
                        'pointName' => $available_point->name,
                        'pointgovernment_name' => $available_point->government->name,
                        'work_type' => 'part Time', // Assuming part time here
                        'point_work_days' => [
                            'dayname' => $available_point->pointDays->map(function ($pointDay) use ($daysOfWeek, $dayWeek) {
                                $index = intval($pointDay->name); // Convert to integer to get the index
                                return [
                                    'is_thisDay_off' => $pointDay->name == $dayWeek ? false : true,
                                    'name' => isset($daysOfWeek[$index]) ? $daysOfWeek[$index] : $pointDay->name,
                                    'from' => $pointDay->from ?? '',
                                    'to' => $pointDay->to ?? '',
                                ];
                            }),
                        ],
                    ];
                }
            }
        
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
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $inspector = GroupTeam::with('group')->whereJsonContains('inspector_ids', $inspectorId)->first();
        $point_group = Grouppoint::whereJsonContains('points_ids', $request->pointID)->value('id');
        $is_added_before = PersonalMission::where('point_id',$point_group)->where('inspector_id',$inspectorId)->where('date',Carbon::today()->toDateString())->get();
      
        if(!($is_added_before->isEmpty())){
           
            return $this->respondError('failed to save', ['error' => 'عفوا تمت أضافه هذه المهمه من قبل لك'], 404);
        }
        $new = new PersonalMission();
        $new->date = Carbon::today()->toDateString();
        $new->inspector_id = $inspectorId;
        $new->point_id  = $point_group;
        $new->group_id  = $inspector->group_id;
        $new->team_id  = $inspector->id;
        $new->save();

        if ($new) {
            return $this->respondSuccess('success', 'Data Saved successfully.');
        } else {
            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
        }
    }
}
