<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\AbsenceEmployee;
use App\Models\AbsenceViolation;
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
        $index = date('w');

        $absences = Absence::whereIn('inspector_id', $inspectorIdsArray)
            ->where('point_id', $request->point_id)
            ->whereDate('date', $today)
            ->where('flag', 1)
            ->get();

        $statusFlag = false;

        $groupedAbsences = $absences->groupBy('mission_id')
            ->filter(function ($group) use (&$statusFlag) {
                foreach ($group as $absence) {
                    if (is_null($absence->status)) {
                        $statusFlag = true;
                        return true;
                    }
                }
                return false;
            });

        if (!$statusFlag) {
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
            }

            $employees_absence = AbsenceEmployee::with(['gradeName', 'absenceType', 'typeEmployee'])
                ->where('absences_id', $absence->id)
                ->get();
            $absence_members = [];
            foreach ($employees_absence as $employee_absence) {
                $absence_members[] = [
                    'employee_name' => $employee_absence->name,
                    'employee_grade' => $employee_absence->grade == null ? '' : $employee_absence->gradeName->name,
                    'employee_military_number' => $employee_absence->military_number ?? '',
                    'employee_type_absence' => $employee_absence->absenceType ? $employee_absence->absenceType->name : '',
                    'type_employee' => $employee_absence->type_employee ? $employee_absence->typeEmployee->name : '',
                    'employee_civil_number' => $employee_absence->civil_number ? $employee_absence->civil_number : '',
                    'employee_file_number' => $employee_absence->file_num ? $employee_absence->file_num : '',
                    'id_employee_grade' => $employee_absence->grade,
                    'id_employee_type_absence' => $employee_absence->absenceType->id,
                    'id_type_employee' => $employee_absence->type_employee

                ];
            }
            $absence_violations = AbsenceViolation::where('absence_id', $absence->id)->get();

            $data = [
                'shift' => $working_time->only(['id', 'name', 'start_time', 'end_time']),
                'abcence_day' => $absence->date,
                'mission_id' => $absence->mission_id,
                'can_update'=> $absence->inspector_id == $inspectorId ? true : false,
                'InspectorId' => $absence->inspector_id ?? null,
                'report_id' => $absence->id,
                'point_id' => $absence->point_id,
                'point_name' => $absence->point->name,
                'point_governate' => $absence->point->government->name,
                'point_location' => $absence->point->google_map,
                'latitude' => $absence->point->lat,
                'longitude' => $absence->point->long,
                'point_time' => $absence->point->work_type == 0 ? 'طوال اليوم' : "من {$time->from} " . ($time->from > 12 ? 'مساءا' : 'صباحا') . " الى {$time->to} " . ($time->to > 12 ? 'مساءا' : 'صباحا'),
                'inspector_name' => $absence->inspector->name,
                'inspector_grade' => auth()->user()->grade_id ? auth()->user()->grade->name : '',
                'team_name' => $teamName,
                'total_number' => $absence->total_number,
                'actual_number' => $absence->actual_number,
                'absence_members' => $absence_members,
                'created_at' => $absence->parent == 0 ? $absence->created_at : Absence::find($absence->parent)->created_at,
                'created_at_time' => $absence->parent == 0 ? $absence->created_at->format('H:i:s') : Absence::find($absence->parent)->created_at->format('H:i:s')
            ];
            foreach ($absence_violations as $absence_violation) {
                $name = '';
                if ($absence_violation->violation_type_id == 1) {
                    $name = "indvidual";
                } else if ($absence_violation->violation_type_id == 2) {
                    $name = "police";
                } else if ($absence_violation->violation_type_id == 3) {
                    $name = "worker";
                } else if ($absence_violation->violation_type_id == 4) {

                    $name = "civil";
                }

                $data[$name] = $absence_violation->actual_number;
            }
            $response[] = $data;
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
        $sectorPoints = Point::where('sector_id', $sector)
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
                "id" => 2,
                "name" => "حضور و غياب"
            ],
            [
                'id' => 1,
                'name' => ' سلوك أنضباطى'
            ],
            [
                "id" => 0,
                "name" => " مبانى",
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

        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value(column: 'name');

        $absenceReport = [];
        $pointViolations = [];

        $violationQuery = Violation::with(['user', 'point', 'violatType', 'instantMission'])->where('status', 1)
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

        if ($type !== null) {
            $violationQuery->where('flag', operator: $type);
        }

        $violations = $violationQuery->orderBy('created_at', 'asc')->get();

        foreach ($violations as $violation) {
            // Get violation types
            $violationTypeIds = explode(',', $violation->violation_type);
            $violationTypes = ViolationTypes::whereIn('id', $violationTypeIds)->get();

            // Include description as a violation type if it exists
            if ($violation->description) {
                $violationTypes->push((object)[
                    'id' => -1,
                    'name' => $violation->description
                ]);
            }

            // Format violation types for output
            $formattedViolationTypes = $violationTypes->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name
                ];
            })->toArray();

            // Format images if they exist
            if ($violation->image) {
                $imageArray = explode(',', $violation->image);
                $formattedImages = implode(', ', $imageArray);
            } else {
                $formattedImages = null;
            }

            // Determine point name and fetch point shift
            $pointName = $violation->point_id ? $violation->point->name : 'لا يوجد نقطه';
            $pointShift = PointDays::where('point_id', $violation->point_id)
                ->where('name', Carbon::parse($violation->created_at)->dayOfWeek)
                ->first();

            // Initialize shift details
            if ($violation->point_id) {
                $shiftDetails = [
                    'start_time' => '00:00',
                    'end_time' => '23:59',
                    'time' => null
                ];

                // Override with actual shift if available
                if ($pointShift && $pointShift->from && $pointShift->to) {
                    $shiftDetails = [
                        'start_time' => $pointShift->from,
                        'end_time' => $pointShift->to,
                        'time' => null // As per requirement
                    ];
                }

                // Handle violations with point_id
                if (!isset($pointViolations[$pointName])) {
                    $pointViolations[$pointName] = [
                        'date' => $violation->created_at->format('Y-m-d'),
                        'is_instansmission' => false,
                        'MissionName' => $violation->flag_instantmission == 1 ? $violation->instantMission->label : null,
                        'description' => $violation->flag_instantmission == 1 ? $violation->instantMission->description : null,
                        'point_id' => $violation->point_id,
                        'point_name' => $pointName,
                        'shift' => $shiftDetails,
                        'team_name' => $teamName,
                        'violationsOfPoint' => []
                    ];
                }

                // Add the violation to the point's violations
                $pointViolations[$pointName]['violationsOfPoint'][] = [
                    'id' => $violation->id,
                    'InspectorName' => $violation->user_id ? $violation->user->name : null,
                    'Inspectorgrade' => $violation->user->grade->name ?? null,
                    'time' => 'وقت و تاريخ التفتيش: ' . $violation->created_at->format('Y-m-d H:i:s'),
                    'name' => $violation->name,
                    'Civil_number' => $violation->Civil_number ?? null,
                    'military_number' => $violation->military_number ?? null,
                    'file_number' => $violation->file_num ?? null,
                    'grade' => grade::where('id', $violation->grade)->select('id', 'name')->first() ?? null,
                    'violation_type' => $formattedViolationTypes,
                    'inspector_name' => $violation->user_id ? $violation->user->name : null,
                    'civil_military' => $violation->civil_type ? ViolationTypes::where('id', $violation->civil_type)->value('name') : null,
                    'image' => $formattedImages ? $formattedImages : null,
                    'created_at' => $violation->parent == 0 ? $violation->created_at : Violation::find($violation->parent)->created_at,
                    'created_at_time' => $violation->parent == 0 ? $violation->created_at->format('H:i:s') : Violation::find($violation->parent)->created_at->format('H:i:s'),
                    'updated_at' => $violation->updated_at,
                    'mission_id' => $violation->mission_id ?? null,
                    'point_id' => $violation->point_id ?? null,
                    'flag_instantmission' => $violation->flag_instantmission,
                    'violation_mode' => $violation->flag,
                ];
            } else {
                // Handle violations without point_id with unique keys
                $noPointKey = 'violation_' . $violation->id; // Create a unique key for this violation
                $pointViolations[$noPointKey] = [
                    'date' => $violation->created_at->format('Y-m-d'),
                    'is_instansmission' => true,
                    'MissionName' => $violation->flag_instantmission == 1 ? $violation->instantMission->label : null,
                    'description' => $violation->flag_instantmission == 1 ? $violation->instantMission->description : null,
                    'point_id' => null,
                    'point_name' => null,
                    'shift' => [
                        'start_time' => null,
                        'end_time' => null,
                        'time' => date("g:i:s A", strtotime($violation->created_at))
                    ],
                    'team_name' => $teamName,
                    'violationsOfPoint' => [
                        [
                            'id' => $violation->id,
                            'InspectorName' => $violation->user_id ? $violation->user->name : null,
                            'Inspectorgrade' => $violation->user->grade->name ?? null,
                            'time' => 'وقت و تاريخ التفتيش: ' . $violation->created_at->format('Y-m-d H:i:s'),
                            'name' => $violation->name,
                            'Civil_number' => $violation->Civil_number ?? null,
                            'military_number' => $violation->military_number ?? null,
                            'file_number' => $violation->file_num ?? null,
                            'grade' => grade::where('id', $violation->grade)->select('id', 'name')->first() ?? null,
                            'violation_type' => $formattedViolationTypes,
                            'inspector_name' => $violation->user_id ? $violation->user->name : null,
                            'civil_military' => $violation->civil_type ? ViolationTypes::where('id', $violation->civil_type)->value('name') : null,
                            'image' => $formattedImages ? $formattedImages : null,
                            'created_at' => $violation->parent == 0 ? $violation->created_at : Violation::find($violation->parent)->created_at,
                            'created_at_time' => $violation->parent == 0 ? $violation->created_at->format('H:i:s') : Violation::find($violation->parent)->created_at->format('H:i:s'),
                            'updated_at' => $violation->updated_at,
                            'mission_id' => $violation->mission_id ?? null,
                            'point_id' => $violation->point_id ?? null,
                            'flag_instantmission' => $violation->flag_instantmission,
                            'violation_mode' => $violation->flag,
                        ]
                    ]
                ];
            }
        }




        // Absences
        foreach ($dates as $date) {
            $today = Carbon::parse($date)->toDateString();
            $index = Carbon::parse($date)->dayOfWeek;

            if ($type === null || $type == 2) {
                $absencesQuery = Absence::where('inspector_id', $inspectorId)->where('flag', 1)
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
                    $pointName = $absence->point_id ? $absence->point->name : null;

                    $team_time = InspectorMission::whereDate('date', $today)
                        ->where('inspector_id', $inspectorId)
                        ->with('workingTime')
                        ->get();
                    // Check if the collection has any items
                    if ($team_time->isNotEmpty() && $team_time->first()->day_off != 1) {
                        // Assuming you want to access the first item
                        $startTimeofTeam = $team_time->first()->workingTime->start_time;
                        $endTimeofTeam = $team_time->first()->workingTime->end_time;
                        $shiftDetails = [
                            'start_time' => $startTimeofTeam,
                            'end_time' => $endTimeofTeam,
                            'time' => null
                        ];
                    } else {
                        $shiftDetails = [
                            'start_time' => null,
                            'end_time' => null,
                            'time' => null
                        ];
                    }
                    $employeesAbsence = AbsenceEmployee::with(['gradeName', 'absenceType', 'typeEmployee'])
                        ->where('absences_id', $absence->id)
                        ->get();
                    $absenceMembers = [];
                    foreach ($employeesAbsence as $employeeAbsence) {
                        $absenceMembers[] = [
                            'employee_name' => $employeeAbsence->name,
                            'employee_grade' => $employeeAbsence->gradeName->name ?? null,
                            'employee_military_number' => $employeeAbsence->military_number ?? null,
                            'employee_type_absence' => $employeeAbsence->absenceType->name ?? null,
                            'type_employee' => $employeeAbsence->typeEmployee->name ?? null,
                            'employee_civil_number' => $employeeAbsence->civil_number ?? null,
                            'employee_file_number' => $employeeAbsence->file_num ?? null,
                        ];
                    }
                    $data = [

                        'abcence_day' => $absence->date,
                        'point_id' => $absence->point_id,
                        'point_name' => $absence->point->name,
                        'point_time' => $pointTime,
                        'shift' => $shiftDetails,
                        'id' => $absence->id,
                        'missiom_id' => $absence->mission_id,
                        'inspector_name' => $absence->inspector->name,
                        'inspector_grade' => auth()->user()->grade_id ? auth()->user()->grade->name : null,
                        'team_name' => $teamName,
                        'total_number' => $absence->total_number,
                        'actual_number' => $absence->actual_number,
                        'disability' => $absence->total_number - $absence->actual_number,
                        'absence_members' => $absenceMembers,
                        'created_at' => $absence->parent == 0 ? $absence->created_at : Absence::find($absence->parent)->created_at,
                        'created_at_time' => $absence->parent == 0 ? $absence->created_at->format('H:i:s') : Absence::find($absence->parent)->created_at->format('H:i:s'),

                    ];

                    $absence_violations = AbsenceViolation::where('absence_id', $absence->id)->get();

                    foreach ($absence_violations as $absence_violation) {
                        $name = '';
                        if ($absence_violation->violation_type_id == 1) {
                            $name = "indvidual";
                        } else if ($absence_violation->violation_type_id == 2) {
                            $name = "police";
                        } else if ($absence_violation->violation_type_id == 3) {
                            $name = "worker";
                        } else if ($absence_violation->violation_type_id == 4) {

                            $name = "civil";
                        }

                        $data[$name] = $absence_violation->actual_number;
                    }

                    $absenceReport[] = $data;
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


        $violation = Violation::where('user_id', auth()->user()->id)->where('status', 1)->whereDate('created_at', $today)->pluck('id')->flatten()->count();
        $is_off = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->value('day_off');
      
        $notifies = Notification::with('mission')
            ->where('user_id', auth()->user()->id)
            ->whereDate('created_at', $today) // Ensure the date comparison is for the correct day
            ->count();
        // dd($notifies);
        $success = [
            'mission_count' => $mission + $mission_instans ?? 0,
            'violation_count' => $violation ?? 0,
            'is_off' => $is_off == 0 ? false : true,
            'notify_num' => $notifies ?? 0
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
            ->where('flag', 0)->where('status', 1)
            ->whereBetween(DB::raw('DATE(created_at)'), [$startOfMonth, $end])
            ->pluck('id')
            ->flatten()
            ->count();

        // Calculate the count of disciplined behavior violations
        $violation_Disciplined_behavior_count = Violation::where('user_id', auth()->user()->id)
            ->where('flag', 1)->where('status', 1)
            ->whereBetween(DB::raw('DATE(created_at)'), [$startOfMonth, $end])
            ->count();
        $is_off = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->value('day_off');
        // Prepare the success response
        $success = [
            'mission_count' => $groupPointsCount + $instantMissionsCount,
            'violation_Disciplined_behavior' => $violation_Disciplined_behavior_count,
            'violations_bulding_count' => $violations_bulding_count,
            'is_off' => $is_off == 0 ? false : true
        ];

        return $this->apiResponse(true, 'Data retrieved successfully.', $success, 200);
    }

    public function getNotifi(Request $request)
    {
        $today = now()->toDateString(); // Today's date
        $notifies = Notification::with('mission')
            ->where('user_id', auth()->user()->id)
            ->whereDate('created_at', $today) // Ensure the date comparison is for the correct day
            ->get();
        // Check if there are any notifications
        if ($notifies->isNotEmpty()) {
            // Extract only the required fields for each notification
            $success['notifi'] = $notifies->map(function ($notification) {
                return [
                    'date' => $notification->created_at->format('Y-m-d') ?? null,
                    'message' => $notification->message,
                    'user_id' => $notification->user_id,
                    'mission_id' => $notification->mission_id,
                    'status' => $notification->status == 0 ?  false : true,
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
