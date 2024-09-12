<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\AbsenceEmployee;
use App\Models\grade;
use App\Models\Grouppoint;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Notification;
use App\Models\Point;
use App\Models\PointDays;
use App\Models\Violation;
use App\Models\ViolationTypes;
use App\Models\WorkingTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\IsFalse;

class reportsController extends Controller
{
    public function dayIndex($date)
    {
        $date = Carbon::parse($date); // Parse the date if it's not already a Carbon instance
        $daysOfWeek = [

            "الأحد",  // 0
            "الإثنين",  // 1
            "الثلاثاء",  // 2
            "الأربعاء",  // 3
            "الخميس",  // 4
            "الجمعة",  // 5
            "السبت",  // 6
        ];
        $dayWeek = $date->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);
        return $index;
    }

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
        // shift
        $inspector = InspectorMission::where('inspector_id', $inspectorId)->where('date', $today)->where('day_off', 0)->first();
        if ($inspector != null) {
            $working_time = WorkingTime::find($inspector->working_time_id);
        } else {
            $working_time = null;
        }
        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value('name');
        $teamInspectors = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->pluck('inspector_ids')->toArray();

        $inspectorIdsArray = [];
        foreach ($teamInspectors as $inspectorIds) {
            $inspectorIdsArray = array_merge($inspectorIdsArray, explode(',', $inspectorIds));
        }
        $index = date('w');;

        $absences = Absence::whereIn('inspector_id', $inspectorIdsArray)
            ->where('point_id', $request->point_id)
            ->whereDate('date', $today)
            ->get();


        $statusFlag = false;

        $groupedAbsences = $absences->groupBy('mission_id')
            ->filter(function ($group) use (&$statusFlag) {
                foreach ($group as $absence) {
                    if (is_null($absence->status)) {
                        $statusFlag = true;
                        return true; // Keep the group if any absence has a null status
                    }
                }
                return false; // Exclude the group if no absence has a null status
            });

        // Debugging grouped absences
        // dd($statusFlag);

        if (!$statusFlag) {
            // dd("false");
            $absences = $absences->filter(function ($absence) {
                return $absence->status === 'Accept';
            });
        } else {

            // $absences = $groupedAbsences;
            $absences = $groupedAbsences->flatten();
            // dd($absences);
        }

        $response = [];  // Initialize the response array

        foreach ($absences as $absence) {
            //
            // dd($absence->point);
            $time = null;
            if ($absence->point->work_type != 0) {
                $time = PointDays::where('point_id', $absence->point_id)
                    ->where('name', $index)
                    ->first();
                // dd($time);
            }

            $employees_absence = AbsenceEmployee::with(['gradeName', 'absenceType', 'typeEmployee'])
                ->where('absences_id', $absence->id)
                ->get();
            $absence_members = [];
            foreach ($employees_absence as $employee_absence) {
                // $grade=$employee_absence->grade != null ? $employee_absence->grade->name : 'لا يوجد رتبه';

                $absence_members[] = [
                    'employee_name' => $employee_absence->name,
                    'employee_grade' => $employee_absence->grade == null ? '' : $employee_absence->gradeName->name,
                    'employee_military_number' => $employee_absence->military_number ?? '',
                    'employee_type_absence' => $employee_absence->absenceType ? $employee_absence->absenceType->name : '',
                    'type_employee' => $employee_absence->type_employee ? $employee_absence->typeEmployee->name : '',
                    'employee_civil_number' => $employee_absence->civil_number ? $employee_absence->civil_number : '',
                    'employee_file_number' => $employee_absence->file_num ? $employee_absence->file_num : ''

                ];
            }
            $response[] = [
                'shift' => $working_time->only(['id', 'name', 'start_time', 'end_time']),
                'abcence_day' => $absence->date,
                'point_name' => $absence->point->name,
                'point_time' => $absence->point->work_type == 0 ? 'طوال اليوم' : "من {$time->from} " . ($time->from > 12 ? 'مساءا' : 'صباحا') . " الى {$time->to} " . ($time->to > 12 ? 'مساءا' : 'صباحا'),
                'inspector_name' => $absence->inspector->name,
                'inspector_grade' => auth()->user()->grade_id ? auth()->user()->grade->name : '',
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
    public function getAllPoints()
    {
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->where('flag', 0)->value('id');
        if (!$inspectorId) {
            return $this->respondError('failed to get data', ['error' => 'عفوا هذا المستخدم لم يعد مفتش'], 404);
        }
        $sector = GroupTeam::with('group.sector')
            ->whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])
            ->first()
            ->group
            ->sector_id;
        // Retrieve the points associated with the sector
        $sectorPoints = Grouppoint::where('sector_id', $sector)->where('deleted', 0)
            ->select('id', 'name')
            ->get()
            ->toArray();
        $response = [];  // Initialize the response array
        foreach ($sectorPoints as $sectorPoint) {
            $response[] = [
                'point_id' => $sectorPoint['id'],
                'point_name' => $sectorPoint['name']
            ];
        }
        $types = [
            [
                "id"=> 2,
                "name"=> "حضور و غياب"
            ],            [
                'id' => 1,
                'name' => ' سلوك أنضباطى'
            ],
            [
                "id"=> 0,
                "name"=>" مبانى",
            ],




        ];
        $success['points'] = $response;
        $success['types'] = $types;
        if ($success) {
            return $this->apiResponse(true, 'Data get successfully.', $success, 200);
        } else {

            return $this->apiResponse(true, 'Data get successfully.', null, 200);
        }
    }
    public function allReportInspector(Request $request)
    {
        $messages = [
            'point_id.*.exists' => 'عفوا هذه النقطه غير متاحه',
            'date.*.date_format' => 'يرجى إدخال التاريخ بصيغه صحيحه yyyy-mm-dd',
        ];

        $validatedData = Validator::make($request->all(), [
            'point_id' => [
                'nullable',
                'array',
            ],
            'point_id.*' => ['nullable', 'integer'],
            'date' => ['nullable', 'array'],
            'date.*' => ['nullable', 'date', 'date_format:Y-m-d'],
            'type_id' => ['nullable', 'integer'],
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $dates = $request->input('date', []);
        if (empty($dates)) {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->toDateString();
            $dates = [];
            while (strtotime($startOfMonth) <= strtotime($endOfMonth)) {
                $dates[] = $startOfMonth;
                $startOfMonth = Carbon::parse($startOfMonth)->addDay()->toDateString();
            }
        } else {
            $dates = array_map(function ($date) {
                return Carbon::parse($date)->toDateString();
            }, $dates);
        }

        $pointIds = $request->input('point_id', []);
        $type = $request->input('type_id');

        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value('name');

        $absenceReport = [];
        $pointViolations = [];

        $violationQuery = Violation::with(['user', 'point', 'violatType'])
            ->where('user_id', auth()->user()->id);

            if (!empty($dates) && count($dates) != 1) {
                $violationQuery->where(function ($query) use ($dates) {
                    foreach ($dates as $date) {
                        $startOfDay = Carbon::parse($date)->startOfDay();
                        $endOfDay = Carbon::parse($date)->endOfDay();
                        $query->orWhereBetween('created_at', [$startOfDay, $endOfDay]);
                    }
                });
            } else {
                $violationQuery->where(function ($query) use ($dates) {
                    foreach ($dates as $date) {
                        $startOfDay = Carbon::parse($date)->startOfDay();
                        $endOfDay = Carbon::parse($date)->endOfDay();
                        $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
                    }
                });
            }


        if (!empty($pointIds)) {
            $violationQuery->whereIn('point_id', $pointIds);
        }

        if ($type !== null)  {
            $violationQuery->where('flag', operator: $type);
        }

        $violations = $violationQuery->orderBy('created_at', 'asc')->get();

        foreach ($violations as $violation) {
            $violationTypeIds = explode(',', $violation->violation_type);
            $violationTypes = ViolationTypes::whereIn('id', $violationTypeIds)->get();

            if ($violation->description) {
                $violationTypes->push((object)[
                    'id' => -1,
                    'name' => $violation->description
                ]);
            }

            $formattedViolationTypes = $violationTypes->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name
                ];
            })->toArray();
            if ($violation->image) {
                $imageArray = explode(',', $violation->image);
                $formattedImages = implode(', ', $imageArray);
            } else {
                $formattedImages = null;
            }

            $pointName = $violation->point_id ? $violation->point->name : 'لا يوجد نقطه';


            // Fetch point shift (work time)
            $pointShift = PointDays::where('point_id', $violation->point_id)
                ->where('name', Carbon::parse($violation->created_at)->dayOfWeek)
                ->first();
            $shiftDetails =  [
                'startTime' => '00:00',  // Full day start time
                'endTime' => '23:59'     // Full day end time
            ]; // Default if no specific shift
            if ($pointShift && $pointShift->from && $pointShift->to) {
                $shiftDetails = $pointShift->only(['from', 'to']);
            }
            if (!isset($pointViolations[$pointName])) {
                $pointViolations[$pointName] = [
                    'date' => $violation->created_at->format('Y-m-d'),
                    'point_id' => $violation->point_id,
                    'point_name' => $pointName,
                    'shift' => $shiftDetails,
                    'team_name' => $teamName,
                    'violationsOfPoint' => []
                ];
            }

            $pointViolations[$pointName]['violationsOfPoint'][] = [
                'id' => $violation->id,
                'InspectorName' => $violation->user_id ? $violation->user->name : 'لا يوجد مفتش',
                'Inspectorgrade' => $violation->user->grade->name ?? '',

                'time' => 'وقت و تاريخ التفتيش: ' . $violation->created_at->format('Y-m-d H:i:s'),
                'name' => $violation->name,
                'Civil_number' => $violation->Civil_number ?? '',
                'military_number' => $violation->military_number ?? '',
                'file_number' => $violation->file_num ?? '',
                'grade' => grade::where('id', $violation->grade)->select('id', 'name')->first() ?? null,
                'violation_type' => $formattedViolationTypes,
                'inspector_name' => $violation->user_id ? $violation->user->name : 'لا يوجد مفتش',
                'civil_military' => $violation->civil_type ? ViolationTypes::where('id', $violation->civil_type)->value('name') : '',
                'image' => $formattedImages ? $formattedImages : null,
                'created_at' => $violation->created_at,
                'updated_at' => $violation->updated_at,
                'mission_id' => $violation->mission_id,
                'point_id' => $violation->point_id,
                'flag_instantmission' => $violation->flag_instantmission,
                'violation_mode' => $violation->flag,
            ];
        }

        // Absences
        foreach ($dates as $date) {
            $today = Carbon::parse($date)->toDateString();
            $index = Carbon::parse($date)->dayOfWeek;

            if ($type === null || $type == 2) {
                $absencesQuery = Absence::where('inspector_id', $inspectorId)
                    ->whereDate('date', $today); // Use whereDate to filter by exact date

                if (!empty($pointIds)) {
                    $absencesQuery->whereIn('point_id', $pointIds);
                }

                $absences = $absencesQuery->get();

                foreach ($absences as $absence) {
                    $time = null;
                    if ($absence->point->work_type != 0) {
                        $time = PointDays::where('point_id', $absence->point_id)
                            ->where('name', $index)
                            ->first();
                    }

                    $pointTime = 'طوال اليوم';
                    if ($time && $time->from && $time->to) {
                        $pointTime = "من {$time->from} " . ($time->from > 12 ? 'مساءا' : 'صباحا') . " الى {$time->to} " . ($time->to > 12 ? 'مساءا' : 'صباحا');
                    }
                    $pointName = $absence->point_id ? $absence->point->name : 'لا يوجد نقطه';


                    // Fetch point shift (work time)
                    $pointShift = PointDays::where('point_id', $absence->point_id)
                        ->where('name', Carbon::parse($absence->created_at)->dayOfWeek)
                        ->first();
                    $shiftDetails =  [
                        'startTime' => '00:00',  // Full day start time
                        'endTime' => '23:59'     // Full day end time
                    ]; // Default if no specific shift
                    if ($pointShift && $pointShift->from && $pointShift->to) {
                        $shiftDetails = $pointShift->only(['from', 'to']);
                    }
                    $employeesAbsence = AbsenceEmployee::with(['gradeName', 'absenceType', 'typeEmployee'])
                        ->where('absences_id', $absence->id)
                        ->get();
                    $absenceMembers = [];
                    foreach ($employeesAbsence as $employeeAbsence) {
                        $absenceMembers[] = [
                            'employee_name' => $employeeAbsence->name,
                            'employee_grade' => $employeeAbsence->gradeName->name ?? '',
                            'employee_military_number' => $employeeAbsence->military_number ?? '',
                            'employee_type_absence' => $employeeAbsence->absenceType->name ?? '',
                            'type_employee' => $employeeAbsence->typeEmployee->name ?? '',
                            'employee_civil_number' => $employeeAbsence->absenceType->name ?? '',
                            'employee_file_number' => $employeeAbsence->file_num ?? '',
                        ];
                    }

                    $absenceReport[] = [

                        'date' => $absence->date,
                        'point_name' => $absence->point->name,
                        'point_time' => $pointTime,
                        'shift' => $shiftDetails,
                        'inspector_name' => $absence->inspector->name,
                        'inspector_grade' => auth()->user()->grade_id ? auth()->user()->grade->name : '',
                        'team_name' => $teamName,
                        'total_number' => $absence->total_number,
                        'actual_number' => $absence->actual_number,
                        'disability' => $absence->total_number - $absence->actual_number,
                        'absence_members' => $absenceMembers,
                    ];
                }
            }
        }
        $success = [
            'report' => $absenceReport,
            'violations' => array_values($pointViolations),
        ];

        return $this->apiResponse(true, 'Data retrieved successfully.', $success, 200);
    }
    public function getAllstatistics(Request $request)
    {
        $today = Carbon::now();
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->where('flag', 0)->value('id');
        if (!$inspectorId) {
            return $this->respondError('failed to get data', ['error' => 'عفوا هذا المستخدم لم يعد مفتش'], 404);
        }
        $mission = InspectorMission::selectRaw('SUM(JSON_LENGTH(ids_group_point)) as count')
            ->where('inspector_id', $inspectorId)
            ->whereDate('date', $today)
            ->value('count');
        $mission_instans = InspectorMission::selectRaw('SUM(JSON_LENGTH(ids_instant_mission)) as count')
            ->where('inspector_id', $inspectorId)
            ->whereDate('date', $today)
            ->value('count');


        $violation = Violation::where('user_id', auth()->user()->id)->whereDate('created_at', $today)->pluck('id')->flatten()->count();
        $success = [
            'mission_count' => $mission + $mission_instans ?? 0,
            'violation_count' => $violation ?? 0,
        ];
        if ($success) {
            return $this->apiResponse(true, 'Data get successfully.', $success, 200);
        } else {

            return $this->apiResponse(true, 'Data get successfully.', null, 200);
        }
    }
    public function getstatistics(Request $request)
    {
        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth()->format('Y-m-d'); // First day of the month
        $end = Carbon::now()->format('Y-m-d'); // End of the period

        $inspectorId = Inspector::where('user_id', auth()->user()->id)
            ->where('flag', 0)
            ->value('id');

        if (!$inspectorId) {
            return $this->respondError('failed to get data', ['error' => 'عفوا هذا المستخدم لم يعد مفتش'], 404);
        }

        // Calculate the count of group points
        $groupPointsCount = InspectorMission::selectRaw('SUM(JSON_LENGTH(ids_group_point)) as count')
            ->where('inspector_id', $inspectorId)
            ->whereBetween('date', [$startOfMonth, $end])
            ->whereNotNull('ids_group_point') // Ensure ids_group_point is not null
            ->whereRaw('JSON_LENGTH(ids_group_point) > 0') // Ensure JSON array is not empty
            ->value('count') ?? 0;

        // Calculate the count of instant missions
        $instantMissionsCount = InspectorMission::selectRaw('SUM(JSON_LENGTH(ids_instant_mission)) as count')
            ->where('inspector_id', $inspectorId)
            ->whereBetween('date', [$startOfMonth, $end])
            ->whereNotNull('ids_instant_mission') // Ensure ids_instant_mission is not null
            ->whereRaw('JSON_LENGTH(ids_instant_mission) > 0') // Ensure JSON array is not empty
            ->value('count') ?? 0;

        // Calculate the count of building violations
        $violations_bulding_count = Violation::where('user_id', auth()->user()->id)
            ->where('flag', 0)
            ->whereBetween('created_at', [$startOfMonth, $end])
            ->pluck('id')
            ->flatten()
            ->count();

        // Calculate the count of disciplined behavior violations
        $violation_Disciplined_behavior_count = Violation::where('user_id', auth()->user()->id)
            ->where('flag', 1)
            ->whereBetween('created_at', [$startOfMonth, $end])
            ->pluck('id')
            ->flatten()
            ->count();
        // Prepare the success response
        $success = [
            'mission_count' => $groupPointsCount + $instantMissionsCount,
            'violation_Disciplined_behavior' => $violation_Disciplined_behavior_count,
            'violations_bulding_count' => $violations_bulding_count,
        ];

        return $this->apiResponse(true, 'Data retrieved successfully.', $success, 200);
    }

    public function getNotifi(Request $request)
    {
        // Get today's date
        $today = Carbon::now();

        // Fetch notifications for the authenticated user, including related mission data
        $notifies = Notification::with(['mission'])->where('user_id', auth()->user()->id)->get();

        // Check if there are any notifications
        if ($notifies->isNotEmpty()) {
            // Extract only the required fields for each notification
            $success['notifi'] = $notifies->map(function($notification) {
                return [
                    'message' => $notification->message,
                    'user_id' => $notification->user_id,
                    'mission_id' => $notification->mission_id,
                    'status'=>$notification->status ==0 ?  false : true,
                ];
            });

            return $this->respondSuccess($success, 'Data retrieved successfully.');
        } else {
            // Return a response if no notifications are found
            return $this->apiResponse(true, 'No notifications found.', null, 200);
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

    public function changeStatus(Request $request)
    {
        $messages = [
            'absence_id.required' => 'point_id required',
        ];
        $validatedData = Validator::make($request->all(), [
            'absence_id' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $abence = Absence::find($request->absence_id);
        $abence->status = "Accept";
        $abence->save();


        if (!$abence) {
            return $this->respondError('Absence not found.', [], 404);
        } else {
            $all = Absence::where('mission_id', $abence->mission_id)->get();

            foreach ($all as $item) {
                $abence = Absence::find($item->absence_id);
                $abence->status = "Accept";
                $abence->save();
            }

            return $this->respondSuccess("success", 'Data Updated successfully.');
        }
    }
}
