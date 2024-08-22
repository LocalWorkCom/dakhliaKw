<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\AbsenceEmployee;
use App\Models\Grouppoint;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\PointDays;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class reportsController extends Controller
{

    public function  getAbsence(Request $request)
    {
        $messages = [
            'point_id.required' => 'يجب اختيار النقطه المضاف لها المهمه',
            'point_id.exists' => 'عفوا هذه النقطه غير متاحه',
        ];

        $validatedData = Validator::make($request->all(), [
            'point_id' => ['required', function ($attribute, $value, $fail) {
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
        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value('name');
        $teamInspectors = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->pluck('inspector_ids')->toArray();

        $inspectorIdsArray = [];
        foreach ($teamInspectors as $inspectorIds) {
            $inspectorIdsArray = array_merge($inspectorIdsArray, explode(',', $inspectorIds));
        }

        $daysOfWeek = [
            "السبت",  // 0
            "الأحد",  // 1
            "الإثنين",  // 2
            "الثلاثاء",  // 3
            "الأربعاء",  // 4
            "الخميس",  // 5
            "الجمعة",  // 6
        ];
        $dayWeek = Carbon::now()->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);

        $absences = Absence::whereIn('inspector_id', $inspectorIdsArray)
            ->where('point_id', $request->point_id)
            ->where('date', $today)
            ->get();

        $response = [];  // Initialize the response array

        foreach ($absences as $absence) {
            $time = null;
            if ($absence->point->work_type != 0) {
                $time = PointDays::where('point_id', $absence->point_id)
                    ->where('name', $index)
                    ->first();
            }

            $employees_absence = AbsenceEmployee::with(['gradeName', 'absenceType'])
                ->where('absences_id', $absence->id)
                ->get();

            $absence_members = [];
            foreach ($employees_absence as $employee_absence) {
                // $grade=$employee_absence->grade != null ? $employee_absence->grade->name : 'لا يوجد رتبه';
                //dd($grade);
                $absence_members[] = [
                    'employee_name' => $employee_absence->name,
                    'employee_grade' => $employee_absence->grade == null ? 'لا يوجد رتبه' : $employee_absence->gradeName->name,
                    'employee_military_number' => $employee_absence->military_number != null ? $employee_absence->military_number : 'لا يوجد رقم عسكرى',
                    'employee_type_absence' => $employee_absence->absenceType ? $employee_absence->absenceType->name : 'غير متوفر',
                ];
            }

            $response[] = [
                'abcence_day' => $absence->date,
                'point_name' => $absence->point->name,
                'point_time' => $absence->point->work_type == 0 ? 'طوال اليوم' : "من {$time->from} " . ($time->from > 12 ? 'مساءا' : 'صباحا') . " الى {$time->to} " . ($time->to > 12 ? 'مساءا' : 'صباحا'),
                'inspector_name' => $absence->inspector->name,
                'team_name' => $teamName,
                'total_number' => $absence->total_number,
                'actual_number' => $absence->actual_number,
                'absence_members' => $absence_members,
            ];
        }

        $success['report'] = $response;


        if ($response) {
            return $this->respondSuccess($success, 'Data get successfully.');
        } else {
           
            return $this->apiResponse(true, 'Data get successfully.', null, 200);
        }
    }
    protected function apiResponse($status, $message, $data, $code, $errorData = null)
    {
        $response = [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        // Only include 'errorData' if it is not null
        if ($errorData !== null) {
            $response['errorData'] = $errorData;
        }

        return response()->json($response, $code);
    }
}
