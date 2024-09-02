<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\AbsenceEmployee;
use App\Models\Grouppoint;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Point;
use App\Models\PointDays;
use App\Models\Violation;
use App\Models\ViolationTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\IsFalse;

class reportsController extends Controller
{
    public function dayIndex($date)
    {
        $date = Carbon::parse($date); // Parse the date if it's not already a Carbon instance
        $daysOfWeek = [
            "السبت",  // 0
            "الأحد",  // 1
            "الإثنين",  // 2
            "الثلاثاء",  // 3
            "الأربعاء",  // 4
            "الخميس",  // 5
            "الجمعة",  // 6
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
        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value('name');
        $teamInspectors = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->pluck('inspector_ids')->toArray();

        $inspectorIdsArray = [];
        foreach ($teamInspectors as $inspectorIds) {
            $inspectorIdsArray = array_merge($inspectorIdsArray, explode(',', $inspectorIds));
        }
        $index = $this->dayIndex(Carbon::today()->toDateString());

        $absences = Absence::whereIn('inspector_id', $inspectorIdsArray)
            ->where('point_id', $request->point_id)
            ->whereDate('created_at', $today)
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


            // $absences = Absence::whereIn('id',$absences->id)->get();

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
          
            $employees_absence = AbsenceEmployee::with(['gradeName', 'absenceType'])
                ->where('absences_id', $absence->id)
                ->get();

            $absence_members = [];
            foreach ($employees_absence as $employee_absence) {
                // $grade=$employee_absence->grade != null ? $employee_absence->grade->name : 'لا يوجد رتبه';
                //dd($grade);
                $absence_members[] = [
                    'employee_name' => $employee_absence->name,
                    'employee_grade' => $employee_absence->grade == null ? '' : $employee_absence->gradeName->name,
                    'employee_military_number' => $employee_absence->military_number != null ? $employee_absence->military_number :'',
                    'employee_type_absence' => $employee_absence->absenceType ? $employee_absence->absenceType->name : '',
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
    public function getAllPoints()
    {
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->where('flag', 0)->value('id');
        if (!$inspectorId) {
            return $this->respondError('failed to get data', ['error' => 'عفوا هذا المستخدم لم يعد مفتش'], 404);
        }
        $sector = GroupTeam::with('group.sector')
            ->whereJsonContains('inspector_ids', $inspectorId)
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
                'id' => 0,
                'name' => 'مخالفه مبانى'
            ],
            [
                'id' => 1,
                'name' => 'مخالفه سلوك أنضباطى'
            ],
            [
                'id' => 2,
                'name' => 'حضور و غياب'
            ]
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
                function ($attribute, $value, $fail) {
                    foreach ($value as $id) {
                        $exists = Grouppoint::whereJsonContains('points_ids', (string) $id)->exists();
                        if (!$exists) {
                            $fail('عفوا هذه النقطه غير متاحه');
                        }
                    }
                },
            ],
            'point_id.*' => ['nullable', 'integer'], // Validate each point_id in the array
            'date' => ['nullable', 'array'], // Allow date to be an array
            'date.*' => ['nullable', 'date', 'date_format:Y-m-d'], // Validate each date in the array
            'type_id' => ['nullable', 'integer'],
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $dates = $request->input('date', [Carbon::today()->toDateString()]);
        $pointIds = $request->input('point_id', []);
        $type = $request->input('type_id');

        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value('name');

        $absenceReport = [];
        $violationReport = [];

        foreach ($dates as $date) {
            $today = Carbon::parse($date)->toDateString(); // Handle date
            $index = $this->dayIndex($today);

            if ($type === null || $type == 2) {

                // Handle absences
                $absencesQuery = Absence::where('inspector_id', $inspectorId)
                    ->whereDate('date', $today);

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

                    $employeesAbsence = AbsenceEmployee::with(['gradeName', 'absenceType'])
                        ->where('absences_id', $absence->id)
                        ->get();

                    $absenceMembers = [];
                    foreach ($employeesAbsence as $employeeAbsence) {
                        $absenceMembers[] = [
                            'employee_name' => $employeeAbsence->name,
                            'employee_grade' => $employeeAbsence->grade == null ? '' : $employeeAbsence->gradeName->name,
                            'employee_military_number' => $employeeAbsence->military_number ?? '',
                            'employee_type_absence' => $employeeAbsence->absenceType ? $employeeAbsence->absenceType->name :'',
                        ];
                    }

                    $absenceReport[] = [
                        'absence_day' => $absence->date,
                        'point_name' => $absence->point->name,
                        'point_time' => $absence->point->work_type == 0 ? 'طوال اليوم' : "من {$time->from} " . ($time->from > 12 ? 'مساءا' : 'صباحا') . " الى {$time->to} " . ($time->to > 12 ? 'مساءا' : 'صباحا'),
                        'inspector_name' => $absence->inspector->name,
                        'team_name' => $teamName,
                        'total_number' => $absence->total_number,
                        'actual_number' => $absence->actual_number,
                        'disability' => $absence->total_number - $absence->actual_number,
                        'absence_members' => $absenceMembers,
                    ];
                }
            }
            if ($type === null || $type == 0 || $type == 1) {

                $violationQuery = Violation::with(['user', 'point', 'violatType'])
                    ->where('user_id', auth()->user()->id)
                    ->whereDate('created_at', $today);

                if (!empty($pointIds)) {
                    $violationQuery->whereIn('point_id', $pointIds);
                }

                if ($type !== null) { // Ensure type is not null or empty
                    $violationQuery->where('flag', $type);
                }

                $violations = $violationQuery->get();
                foreach ($violations as $violation) {
                    $violationTypeIds = explode(',', $violation->violation_type); // Convert to array if needed
                    $violationTypes = ViolationTypes::whereIn('id', $violationTypeIds)->pluck('name')->toArray();
                    // Format names into a string
                    $violationTypeNames = implode(', ', $violationTypes);
                    $formattedViolationType = $violationTypeNames ? "مخالفة سلوك انضباطى ({$violationTypeNames})" : 'غير متوفر';
                    $imageData = $violation->image; // e.g., "/Api/images/violations/1724759522.png,/Api/images/violations/1724759522.png"

                    // Split the comma-separated image paths into an array
                    $imageArray = explode(',', $imageData);

                    // Count the number of images
                    $imageCount = count($imageArray);

                    // Prepare the formatted image string
                    $formattedImages = $imageCount . ' صور ,' . ' [' . implode(', ', $imageArray) . ']';
                    $violationReport[] = [
                        'date' => $violation->created_at->format('Y-m-d') . ' ' . 'وقت و تاريخ التفتيش' . ' ' . $violation->created_at->format('H:i:s'),
                        'name' => $violation->name,
                        'Civil_number' => $violation->Civil_number ? $violation->Civil_number : '',
                        'military_number' => $violation->military_number ? $violation->military_number :'',
                        'grade' => $violation->grade ? $violation->grade :'',
                        'violation_type' => $violation->flag == 0 ? 'مخالفة مبانى' : $formattedViolationType,
                        'point_name' => $violation->point->name,
                        'inspector_name' => $violation->user->name,
                        'images' => $formattedImages,
                    ];
                }
            }
        }

        $success = [
            'absences' => $absenceReport ?? [],
            'violations' => $violationReport ?? [],
        ];
        //end violation Building report
        if ($success) {
            return $this->apiResponse(true, 'Data get successfully.', $success, 200);
        } else {

            return $this->apiResponse(true, 'Data get successfully.', null, 200);
        }
    }


    public function getAllstatistics(Request $request)
    {
        $today = Carbon::now();
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->where('flag', 0)->value('id');
        if (!$inspectorId) {
            return $this->respondError('failed to get data', ['error' => 'عفوا هذا المستخدم لم يعد مفتش'], 404);
        }
        $mission = InspectorMission::where('inspector_id', $inspectorId)->whereDate('date', $today)->pluck('ids_group_point')->flatten()
            ->count();
        $violation = Violation::where('user_id', auth()->user()->id)->whereDate('created_at', $today)->pluck('id')->flatten()->count();
        $success = [
            'mission_count' => $mission ?? 0,
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
        $startOfMonth = $today->startOfMonth()->format('Y-m-d');
        $endOfMonth = $today->endOfMonth()->format('Y-m-d');
       
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->where('flag', 0)->value('id');
        if (!$inspectorId) {
            return $this->respondError('failed to get data', ['error' => 'عفوا هذا المستخدم لم يعد مفتش'], 404);
        }
        $mission_count = InspectorMission::where('inspector_id', $inspectorId)->whereDate('date', $today)->pluck('ids_group_point')->flatten()
            ->flatten()->count();
        $violations_bulding_count = Violation::where('user_id', auth()->user()->id)->where('flag',0)->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->pluck('id')->flatten()->count();
        $violation_Disciplined_behavior_count = Violation::where('user_id', auth()->user()->id)->where('flag',1)->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->pluck('id')->flatten()->count();

        // $point_ids = Grouppoint::whereIn('id', $mission)->pluck('points_ids')->flatten()->toArray();
        // $points_detail = Point::with(['pointDays'])->whereIn('id', $point_ids)->get();


        // $availablegroup_points = Grouppoint::where('id', $mission)
        //     ->get();

        // $All_points = [];

        // // Process available group points
        // $availablegroup_points->each(function ($grouppoint) use (&$All_points, $daysOfWeek, $index, $dayWeek) {
        //     // Fetch points related to the group point
        //     $available_points = Point::with(['pointDays'])->whereIn('id', $grouppoint->points_ids)->get();
        //     $name = $grouppoint->flag == 0 ? 'لا توجد مجموعه' : $grouppoint->name;

        //     $available_points->each(function ($available_point) use (&$All_points, $daysOfWeek, $index, $dayWeek, $name) {
        //         if ($available_point->work_type == 0) {
        //             // Check if today's day is in days_work
        //             $is_off = in_array($index, $available_point->days_work);

        //             if ($is_off) {
        //                 $All_points[] = [
        //                     'point_governate' => $available_point->government->name,
        //                     'point_id' => $available_point->id,
        //                     'point_name' => $available_point->name,
        //                     'point_GroupName' => $name ?? 'Unknown',
        //                     'point_time' => 'طوال اليوم',
        //                     'point_location' => $available_point->google_map,
        //                 ];
        //             }
        //         } else {
        //             $pointDay =  $available_point->pointDays->where('name', $index)->first();
        //             $All_points[] = [
        //                 'point_governate' => $available_point->government->name,
        //                 'point_id' => $available_point->id,
        //                 'point_name' => $available_point->name,
        //                 'point_GroupName' => $name ?? 'Unknown',
        //                 'point_time' => "من {$pointDay->from} " . ($pointDay->from > 12 ? 'مساءا' : 'صباحا') . " الى {$pointDay->to} " . ($pointDay->to > 12 ? 'مساءا' : 'صباحا'),

        //                 'point_location' => $available_point->google_map,
        //             ];
        //         }
        //     });
        // });

        // $violations = Violation::with(['user', 'point', 'violatType'])->where('user_id', auth()->user()->id)->whereDate('created_at', $today)->get();

        // foreach ($violations as $violation) {
        //     $violationTypeIds = explode(',', $violation->violation_type); // Convert to array if needed
        //     $violationTypes = ViolationTypes::whereIn('id', $violationTypeIds)->pluck('name')->toArray();
        //     // Format names into a string
        //     $violationTypeNames = implode(', ', $violationTypes);
        //     $formattedViolationType = $violationTypeNames ? "مخالفة سلوك انضباطى ({$violationTypeNames})" : 'غير متوفر';
        //     $imageData = $violation->image; // e.g., "/Api/images/violations/1724759522.png,/Api/images/violations/1724759522.png"

        //     // Split the comma-separated image paths into an array
        //     $imageArray = explode(',', $imageData);

        //     // Count the number of images
        //     $imageCount = count($imageArray);

        //     // Prepare the formatted image string
        //     $formattedImages = $imageCount . ' صور ,' . ' [' . implode(', ', $imageArray) . ']';
        //     $violationReport[] = [
        //         'date' => $violation->created_at->format('Y-m-d') . ' ' . 'وقت و تاريخ التفتيش' . ' ' . $violation->created_at->format('H:i:s'),
        //         'name' => $violation->name,
        //         'Civil_number' => $violation->Civil_number ? $violation->Civil_number : 'لا يوجد رقم مدنى',
        //         'military_number' => $violation->military_number ? $violation->military_number : 'لا يوجد رقم مدنى',
        //         'grade' => $violation->grade ? $violation->grade : 'لا يوجد رتبه',
        //         'violation_type' => $violation->flag == 0 ? 'مخالفة مبانى' : $formattedViolationType,
        //         'point_name' => $violation->point_id ? $violation->point->name : 'لا يوجد نقطه',
        //         'inspector_name' => $violation->user->name,
        //         'images' => $formattedImages,
        //     ];
        // }
        $success = [
            'mission_count' => $mission_count ?? 0,
            'violation_Disciplined_behavior' => $violation_Disciplined_behavior_count ??0,
            'violations_bulding_count' => $violations_bulding_count ??0,

        ];
        if ($success) {
            return $this->apiResponse(true, 'Data get successfully.', $success, 200);
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
        }
        else
        {
            $all= Absence::where('mission_id',$abence->mission_id)->get();

            foreach($all as $item)
            {
                $abence = Absence::find($item->absence_id);
                $abence->status = "Accept";
                $abence->save();
            }
    
            return $this->respondSuccess("success", 'Data Updated successfully.');
        }
        

    }
}
