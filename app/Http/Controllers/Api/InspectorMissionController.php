<?php

namespace App\Http\Controllers\Api;

use App\Models\Attendance;
use App\Models\paperTransaction;
use App\Models\PersonalMission;
use App\Models\Violation;
use Carbon\Carbon;

use App\Models\Grouppoint;
use Illuminate\Http\Request;
use App\Models\instantmission;
use App\Models\InspectorMission;
use App\Models\Inspector;
use App\Models\Point;
use App\Models\PointDays;
use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\AttendanceEmployee;
use App\Models\departements;
use App\Models\ForceName;
use App\Models\grade;
use App\Models\GroupTeam;
use App\Models\PointOption;
use App\Models\User;
use App\Models\ViolationTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use IntlDateFormatter;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;
class InspectorMissionController extends Controller
{
    function isTimeAvailable($pointStart, $pointEnd)
    {
        $currentTime = Carbon::now()->format('H:i');

        // Convert the times to Carbon instances for easy comparison
        $start = Carbon::createFromTimeString($pointStart);
        $end = Carbon::createFromTimeString($pointEnd)->addMinutes(30);
        $current = Carbon::createFromTimeString($currentTime);

        return $current->between($start, $end);
    }
    public function getMissionsByInspector()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Retrieve the currently authenticated inspector
        $inspector = Inspector::where('user_id', Auth::id())->first();

        if (!$inspector) {
            return $this->respondError('Inspector not found.', 404);
        }

        $inspectorId = $inspector->id;

        // Get the inspector's shift for today
        $team_time = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->with('workingTime')
            ->first();

        if ($team_time && $team_time->day_off != 1) {
            $inspector_shift = [
                'name' => $team_time->workingTime->name ?? 'غير محدد',
                'start_time' => $team_time->workingTime->start_time ?? '00:00',
                'end_time' => $team_time->workingTime->end_time ?? '23:59'
            ];
        } else {
            $inspector_shift = [
                'name' => 'طوال اليوم',
                'start_time' => '00:00',
                'end_time' => '23:59'
            ];
        }

        // Get missions for the specific inspector
        $missions = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->get();

        $missionData = [];
        $instantMissionData = [];
        $missionCount = 0;
        $instantMissionCount = 0;

        foreach ($missions as $mission) {
            $idsGroupPoint = is_array($mission->ids_group_point) ? $mission->ids_group_point : explode(',', $mission->ids_group_point ?? '');

            foreach ($idsGroupPoint as $groupId) {
                $groupPoint = Grouppoint::with('government', 'sector')->find($groupId);

                if ($groupPoint) {
                    $idsPoints = is_array($groupPoint->points_ids) ? $groupPoint->points_ids : explode(',', $groupPoint->points_ids ?? '');
                    $groupPointsData = [];

                    foreach ($idsPoints as $pointId) {
                        $point = Point::with('government', 'sector')->find($pointId);

                        if ($point) {
                            $todayName = date('w');
                            $inspectionTime = 'طوال اليوم';
                            $available = true;

                            if ($point->work_type == 1) {
                                $workTime = PointDays::where('point_id', $pointId)->where('name', $todayName)->first();

                                if ($workTime) {
                                    $fromTime = $workTime->from;
                                    $toTime = $workTime->to;

                                    $inspectionTime = "من {$fromTime} " . ($fromTime > 12 ? 'مساءا' : 'صباحا') . " إلى {$toTime} " . ($toTime > 12 ? 'مساءا' : 'صباحا');
                                    $available = $this->isTimeAvailable($fromTime, $toTime);
                                } else {
                                    $available = false;
                                }
                            }

                            $date = Carbon::today()->format('Y-m-d');
                            $violationCount = Violation::where('point_id', $point->id)->where('status', 1)->whereDate('created_at', $date)->count();
                            $absenceCount = Absence::where('point_id', $point->id)->where('flag', 1)->whereDate('date', $date)->count();
                            $paperCount = paperTransaction::where('point_id', $point->id)->where('status', 1)->whereDate('date', $date)->count();

                            $groupPointsData[] = [
                                'point_id' => $point->id,
                                'point_name' => $point->name,
                                'point_governate' => $point->government->name ?? 'N/A',
                                'point_time' => $inspectionTime,
                                'point_location' => $point->google_map ?? null,
                                'point_availability' => $available,
                                'latitude' => $point->lat,
                                'longitude' => $point->long,
                                'is_visited' => ($violationCount > 0 || $absenceCount > 0 || $paperCount > 0),
                                'count_violation' => $violationCount,
                                'count_absence' => $absenceCount
                            ];
                        }
                    }

                    $missionData[] = [
                        'mission_id' => $mission->id,
                        'inspector_shift' => $inspector_shift,
                        'governate' => $groupPoint->government->name ?? 'N/A',
                        'sector' => $groupPoint->sector->name ?? 'N/A',
                        'name' => $groupPoint->name,
                        'points_count' => count($groupPointsData),
                        'points' => $groupPointsData,
                        'created_at' => $mission->created_at
                    ];
                }
            }

            // Handle instant missions
            $instantMissions = is_array($mission->ids_instant_mission) ? $mission->ids_instant_mission : explode(',', $mission->ids_instant_mission ?? '');

            foreach ($instantMissions as $instantId) {
                $instantMission = InstantMission::find($instantId);

                if ($instantMission) {
                    $instantMissionCount++;

                    $createdAt = $instantMission->created_at;
                    $timeArabic = ($createdAt->format('A') === 'AM') ? 'صباحا' : 'مساءا';

                    $instantMissionData[] = [
                        'instant_mission_id' => $instantMission->id,
                        'name' => $instantMission->label,
                        'location' => $instantMission->location ?? null,
                        'description' => $instantMission->description,
                        'group' => $instantMission->group->name ?? 'N/A',
                        'team' => $instantMission->groupTeam->name ?? 'N/A',
                        'date' => $createdAt->format('Y-m-d'),
                        'time' => $createdAt->format('h:i'),
                        'time_name' => $timeArabic,
                        'latitude' => $instantMission->latitude,
                        'longitude' => $instantMission->longitude
                    ];
                }
            }
        }

        $missionCount = count($missionData);
        $dayNamesArabic = [
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت'
        ];

        $responseData = [
            'date' => $today,
            'date_name' => $dayNamesArabic[date('l')],
            'mission_count' => $missionCount,
            'instant_mission_count' => $instantMissionCount,
            'inspector_shift' => $inspector_shift,
            'missions' => $missionData,
            'instant_missions' => $instantMissionData
        ];

        return $this->respondSuccess($responseData, 'Data retrieved successfully.');
    }



    /**
     * Lizamat
     */
    public function get_shift(Request $request)
    {
        $inspector = Inspector::where('user_id', Auth::id())->first();
        // $inspector=Auth::user()->inspectorId;
        //dd($inspector);
        if ($request->date) {
            $date = $request->date;
        } else {
            $date = date('Y-m-d');
        }

        $todayMission = InspectorMission::with('workingTime', 'workingTree')->where('date', $date)->where('inspector_id', $inspector->id)->first();
        /*   if($todayMission->day_off==1)
        {
            $success['dayOff'] = 1;
            return $this->respondSuccess(json_decode('{"dayOff":1}'), 'يوم راحة لايوجد دوام');

        }else{ */

        if ($todayMission) {

            if ($todayMission->day_off == 0) {
                $success['dayOff'] = 0;
                $success['name'] = $todayMission->workingTree->name;
                $success['workdays'] = $todayMission->workingTree->working_days_num;
                $success['holidaydays'] = $todayMission->workingTree->holiday_days_num;
                $success['todayTimes_start'] = $todayMission->workingTime->start_time;
                $success['todayTimes_end'] = $todayMission->workingTime->end_time;
            } else {
                $success['dayOff'] = 0;
                $success['name'] = null;
                $success['workdays'] = null;
                $success['holidaydays'] = null;
                $success['todayTimes_start'] = null;
                $success['todayTimes_end'] = null;
            }


            return $this->respondSuccess($success, 'بيانات اللازم اليوم');
        }

        return $this->apiResponse(true, 'No Data .', null, 200);



        // }
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
    public function get_Alltypes(Request $request)
    {
        // dd($request->input('type', []));
        $type = ViolationTypes::where('type_id', '0')->get();
        $grade = grade::where('type', 0)->get();
        $names = ForceName::get();
        $departments = departements::whereNot('id', 1)->get();
        $countries = getCountries();
        $success['countries'] = $countries;
        $absenceType = AbsenceType::all();
        $allViolationType = ViolationTypes::whereJsonContains('type_id',  $request->type)->get();

        if ($grade->isNotEmpty()) {
            $success['grade2'] = $grade->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['grade2'] = '';
        }
        $grade3 = grade::where('type', 2)->get();

        if ($grade->isNotEmpty()) {
            $success['grade3'] = $grade3->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['grade3'] = '';
        }
        if ($type->isNotEmpty()) {
            $success['type'] = $type->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['type'] = '';
        }
        if ($absenceType->isNotEmpty()) {
            $success['absence_Type'] = $absenceType->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['absence_Type'] = "لا يوجد بيانات";
        }
        $success['ViolationType'] = $allViolationType->map(function ($item) {
            return $item->only(['id', 'name']);
        });
        $success['names'] = $names->map(function ($item) {
            return $item->only(['id', 'name']);
        });
        $success['departments'] = $departments->map(function ($item) {
            return $item->only(['id', 'name']);
        });
        return $this->respondSuccess($success, 'Get Data successfully.');
    }

    public function addAttendance(Request $request)
    {
        $messages = [
            'instant_mission_id.required' => 'يرجى ارسال رقم امر الخدمه',
        ];

        $validatedData = Validator::make($request->all(), [
            'instant_mission_id' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        // if(!checkShift()){
        //     return $this->respondError('Validation Error.', 'لا يمكن تسجيل المخالفه خارج مواعيد العمل ', 400);

        //  }
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->first();
        if (!$request->id) {

            // $attendanceCount = $request->has('AtendanceEmployee') ? count($request->input('AtendanceEmployee')) : 0;
            $totalNames = array_sum(array_column($request['AtendanceEmployee'], 'name'));

           // dd($totalNames);

            $attendance = new Attendance();
            $attendance->date = Carbon::now()->format('Y-m-d');
            // $attendance->mission_id = $request->mission_id ?? null;
            $attendance->instant_id = $request->instant_mission_id;
            $attendance->total = $totalNames;
            $attendance->inspector_id = $inspectorId->id;
            $attendance->parent = null;
            $attendance->flag = 1;
            $attendance->save();

            $attendanceEmployees = [];

            if ($attendance) {
                if ($request->has('AtendanceEmployee')) {
                    $att_total=0;
                    foreach ($request->AtendanceEmployee as $item) {

                        $employeeValidator = Validator::make($item, [
                            'name' => 'required',
                            'type_employee' => 'required',
                            // 'grade' => 'required'
                        ], $messages);

                        if ($employeeValidator->fails()) {
                            return $this->respondError('Validation Error.', $employeeValidator->errors(), 400);
                        }

                        $emp = new AttendanceEmployee();
                        $emp->name = $item['name'];
                        $emp->grade_id = $item['grade'] ?? null;
                        $emp->type_id = $item['type_employee'] ?? null;
                        $emp->attendance_id = $attendance->id;
                        $emp->force_id = $item['department'] ?? null;
                        $emp->save();
                        $att_total+=intval($item['name']);

                        $attendanceEmployees[] = $emp;
                    }
                    $attendance->total =  $att_total;

                    $attendance->save();
                }
            }
        } else {
            $record = Attendance::where('id', $request->id)->first();
            $isParent = $record->parent;
            if ($isParent == null) {
                $record->flag = 0;
                $record->save();
                // $attendanceCount = $request->has('AtendanceEmployee') ? count($request->input('AtendanceEmployee')) : 0;
                $totalNames = array_sum(array_column($request['AtendanceEmployee'], 'name'));

                $attendance = new Attendance();
                $attendance->date = Carbon::now()->format('Y-m-d');
                //$attendance->mission_id = $request->mission_id;
                $attendance->instant_id = $request->instant_mission_id;
                $attendance->total = $totalNames;
                $attendance->inspector_id = $inspectorId->id;
                $attendance->parent = $request->id;
                $attendance->flag = 1;
                $attendance->save();

                $attendanceEmployees = [];

                if ($attendance) {
                    if ($request->has('AtendanceEmployee')) {
                        $att_total=0;
                        foreach ($request->AtendanceEmployee as $item) {

                            $employeeValidator = Validator::make($item, [
                                'name' => 'required',
                                'type_employee' => 'required',
                                // 'grade' => 'required'
                            ], $messages);

                            if ($employeeValidator->fails()) {
                                return $this->respondError('Validation Error.', $employeeValidator->errors(), 400);
                            }

                            $emp = new AttendanceEmployee();
                            $emp->name = $item['name'];
                            $emp->grade_id = $item['grade'] ?? null;
                            $emp->type_id = $item['type_employee'] ?? null;
                            $emp->attendance_id = $attendance->id;
                            $emp->force_id = $item['department'] ?? null;
                            $emp->save();
                            $att_total+=intval($item['name']);
                            $attendanceEmployees[] = $emp;
                        }
                        $attendance->total=$att_total;
                        $attendance->save();
                    }
                }
            } else {
                $records = Attendance::where('parent', $isParent)->pluck('id')->toArray();
                foreach ($records as $recordId) {
                    $recs = Attendance::find($recordId);
                    $recs->flag = 0;
                    $recs->save();
                }
                $totalNames = array_sum(array_column($request['AtendanceEmployee'], 'name'));

                $attendance = new Attendance();
                $attendance->date = Carbon::now()->format('Y-m-d');
                //$attendance->mission_id = $request->mission_id;
                $attendance->instant_id = $request->instant_mission_id;
                $attendance->total = $totalNames;
                $attendance->inspector_id = $inspectorId->id;
                $attendance->parent = $isParent;
                $attendance->flag = 1;
                $attendance->save();

                $attendanceEmployees = [];

                if ($attendance) {
                    if ($request->has('AtendanceEmployee')) {
                        $att_total=0;
                        foreach ($request->AtendanceEmployee as $item) {

                            $employeeValidator = Validator::make($item, [
                                'name' => 'required',
                                'type_employee' => 'required',
                                //'grade' => 'required'
                            ], $messages);

                            if ($employeeValidator->fails()) {
                                return $this->respondError('Validation Error.', $employeeValidator->errors(), 400);
                            }

                            $emp = new AttendanceEmployee();
                            $emp->name = $item['name'];
                            $emp->grade_id = $item['grade'] ?? null;
                            $emp->type_id = $item['type_employee'] ?? null;
                            $emp->attendance_id = $attendance->id;
                            $emp->force_id = $item['department'] ?? null;
                            $emp->save();
                            $att_total+=intval($item['name']);
                            $attendanceEmployees[] = $emp;
                        }

                        $attendance->total=$att_total;
                        $attendance->save();
                    }
                }
            }
        }

        $success = [
            'Attendance' => $attendance,
            'AttendanceEmployees' => $attendanceEmployees,
        ];

        return $this->respondSuccess($success, 'Data Saved successfully.');
    }

    public function getAllAttendance(Request $request)
    {
        $messages = [
            'mission_id.required' => 'يرجى ارسال رقم امر الخدمه',
        ];

        $validatedData = Validator::make($request->all(), [
            'mission_id' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $inspector = Inspector::where('user_id', auth()->user()->id)->first();
        $inspectorId = $inspector ? $inspector->id : null;
        $instantMission = InstantMission::with('group', 'groupTeam')->findOrFail($request->mission_id);

        // Handle GIS link or location
        $location = str_contains($instantMission->location, 'gis.paci.gov.kw') ? null : $instantMission->location;
        $kwFinder = str_contains($instantMission->location, 'gis.paci.gov.kw') ? $instantMission->location : null;

        $time = Carbon::parse($instantMission->created_at)->format('H:i');
        $timeArabic = ($instantMission->created_at->format('A') === 'AM') ? 'صباحا' : 'مساءا';

        $attendanceRecords = Attendance::where('instant_id', $request->mission_id)
            ->where('flag', 1)
            ->get();

        $success['Attendance'] = $attendanceRecords->map(function ($attendance) use ($instantMission, $location, $kwFinder, $time, $timeArabic, $inspectorId) {
            $attendanceEmployees = AttendanceEmployee::with('force', 'grade')->where('attendance_id', $attendance->id)->get();
            $inspector = Inspector::find($attendance->inspector_id);
            $user = $inspector ? User::with('grade')->find($inspector->user_id) : null;
            $name = $user ? $user->name : 'N/A';
            $grade = $user && $user->grade ? $user->grade->name : 'N/A';

            $forceData = $attendanceEmployees->unique('force_id')
                ->map(function ($employee) {
                    return [
                        'force_id' => $employee->force_id,
                        'force_name' => $employee->force ? $employee->force->name : 'Unknown'
                    ];
                })
                ->toArray();

            $createdAt = $attendance->parent == 0 ? $attendance->created_at : Attendance::find($attendance->parent)->created_at;

            return [
                'id' => $attendance->id,
                'force_name' => 'ادارة (' . implode(', ', array_column($forceData, 'force_name')) . ')',
                'total_force' => $attendanceEmployees->sum('name'), // Sum of all `name` values
                'total_police' => $attendanceEmployees->where('type_id', 2)->sum('name'), // Sum of `name` for type_id = 2
                'total_individuals' => $attendanceEmployees->where('type_id', 1)->sum('name'), // Sum of `name` for type_id = 1
                'total_workers' => $attendanceEmployees->where('type_id', 3)->sum('name'), // Sum of `name` for type_id = 3
                'total_civilian' => $attendanceEmployees->where('type_id', 4)->sum('name'),
                'force_names' => $attendanceEmployees->map(function ($emp, $index) {
                    return [
                        'index' => $index + 1,
                        'name' => $emp->name,
                        'type' => $emp->type->name,
                        'type_id' => $emp->type_id,
                        'force_id' => $emp->force_id,
                        'force_name' => $emp->force ? $emp->force->name : 'Unknown',
                    ];
                }),
                'created_at' => $createdAt,
                'created_at_time' => $createdAt->format('H:i:s'),
                'inspector_name' => $name,
                'inspector_grade' => $grade,
                'can_update' => $attendance->inspector_id == $inspectorId ? true : false,

                'instantMissions' => [
                    'instant_mission_id' => $instantMission->id,
                    'name' => $instantMission->label,
                    'location' => $location,
                    'KWfinder' => $kwFinder,
                    'description' => $instantMission->description,
                    'group' => $instantMission->group_id ? $instantMission->group->name : 'N/A',
                    'team' => $instantMission->group_team_id ? $instantMission->groupTeam->name : 'N/A',
                    'date' => $instantMission->created_at->format('Y-m-d'),
                    'time' => $time,
                    'time_name' => $timeArabic,
                    'latitude' => $instantMission->latitude,
                    'longitude' => $instantMission->longitude,
                    'attachment' => $instantMission->attachment,

                ]
            ];
        });

        return $this->respondSuccess($success, 'Data Saved successfully.');
    }
}
