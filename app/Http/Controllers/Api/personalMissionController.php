<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Point;
use Carbon\Carbon;
use Illuminate\Http\Request;

class personalMissionController extends Controller
{
    public function getAllPoints(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $id = auth()->user()->inspectorId;
        $inspector = GroupTeam::with('group')->whereJsonContains('inspector_ids', $id)->first();
        dd($id);
        if ($inspector) {
            $inspector_points = InspectorMission::where('inspector_id', $id)
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

            $available_points = Point::whereIn('id', $availablegroup_points)->get();

           
        }
        //dd($available_points);
        // if ($available_points) {

        //     $success['$available_points'] = $available_points;
        //     return $this->respondSuccess($success, 'Get Data successfully.');
        // } else {
        //     return $this->respondError('type not found', ['error' => 'خطأ فى استرجاع البيانات'], 404);
        // }

        // 
    }
    public function addPersonalMission(Request $request)
    {

        $messages = [
            'pointID.required' => 'يجب اختيار النقطه المضاف لها المهمه',
            'pointID.exists' => 'عفوا هذه النقطه غير متاحه',
        ];
        $validatedData = Validator::make($request->all(), [
            'pointID' => 'required|exists:group_points,id',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $new = new PersonalMission();
        $new->date = Carbon::today()->toDateString();
        $new->inspector_id = auth()->user()->id;
        $new->save();














        if ($new) {
            $success['violation'] = $new->only(['id', 'name', 'military_number', 'Civil_number', 'grade', 'image', 'violation_type', 'user_id']);
            return $this->respondSuccess($success, 'Data Saved successfully.');
        } else {
            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
        }
    }
}
