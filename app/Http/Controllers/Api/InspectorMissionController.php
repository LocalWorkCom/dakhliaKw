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
use App\Models\ForceName;
use App\Models\grade;
use App\Models\GroupTeam;
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
        // Retrieve the currently authenticated user
        $inspector = Inspector::where('user_id', Auth::id())->first();
        // dd($inspectorId);
        $inspectorId = $inspector->id;
        $team_time = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->with('workingTime')
            ->get();
        // Check if the collection has any items
        if ($team_time->isNotEmpty() && $team_time->first()->day_off != 1) {

            // $startTimeofTeam = $team_time->first()->workingTime->start_time;
            // $endTimeofTeam = $team_time->first()->workingTime->end_time;
            // $name = $team_time->first()->workingTime->name;
            $inspector_shift = [
                'name' => $team_time->first()->workingTime->name,
                'start_time' => $team_time->first()->workingTime->start_time,
                'end_time' => $team_time->first()->workingTime->end_time
            ];
        } else {
            $inspector_shift = [
                'name' => 'طوال اليوم',
                'start_time' => '00:00',
                'end_time' => '23:59'
            ];
        }
        // $currentTime = Carbon::now()->format('H:i');
        // $isBetween = Carbon::parse($team_time->first()->workingTime->start_time)->isBefore($currentTime) && Carbon::parse($team_time->first()->workingTime->end_time)->isAfter($currentTime);
        // if (!$isBetween) {
        // }
        // else{

        // }
        // Retrieve the missions for the specific inspector
        $missions = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            // ->with('workingTime', 'groupPoints.government')
            ->get();
        $instantmissioncount = 0; //$instantmissions->count();
        $missionCount = 0; //$missions->count();

        $count = 0;
        $missionData = [];
        $avilable = true;
        $groupPointCount = 0;
        $instantMissionData = [];
        foreach ($missions as $mission) {
            $idsGroupPoint = is_array($mission->ids_group_point) ? $mission->ids_group_point : explode(',', $mission->ids_group_point);
            if (!is_null($mission->ids_instant_mission)) {
                // The variable is null
                $instantMissions = is_array($mission->ids_instant_mission) ? $mission->ids_instant_mission : explode(',', $mission->ids_instant_mission);
            } else {
                $instantMissions = $mission->ids_instant_mission;
            }
            $groupPointCount = count($idsGroupPoint);
            $count += $groupPointCount;
            foreach ($idsGroupPoint as $groupId) {
                $groupPointsData = [];
                $groupPoint = Grouppoint::with('government', 'sector')->find($groupId);
                if ($groupPoint) {
                    $idsPoints = is_array($groupPoint->points_ids) ? $groupPoint->points_ids : explode(',', $groupPoint->points_ids);
                    $groupPointsData = [];

                    foreach ($idsPoints as $pointId) {
                        $point = Point::with('government')->find($pointId);
                        if ($point) {
                            $today = date('w');
                            $inspectionTime = '';
                            $avilable = false;
                            $pointTime = ['startTime' => '00:00', 'endTime' => '23:59']; // Default to full day

                            if ($point->work_type == 1) {
                                $workTime = PointDays::where('point_id', $pointId)->where('name', $today)->first();

                                if ($workTime) {

                                    $startTime = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' ' . $workTime->from);

                                    $endtTime = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . ' ' . $workTime->to);

                                    $fromTime = $startTime->format('H:i');
                                    $toTime = $endtTime->format('H:i');

                                    $inspectionTime = "من {$fromTime} " . ($workTime->from > 12 ? 'مساءا' : 'صباحا') . " الى {$toTime} " . ($workTime->to > 12 ? 'مساءا' : 'صباحا');
                                    $avilable = $this->isTimeAvailable($fromTime, $toTime);

                                    $pointTime = ['startTime' => $fromTime, 'endTime' => $toTime];
                                } else {

                                    // If working time is not found, default to full day
                                    $inspectionTime = 'طول اليوم';
                                    $avilable = false;
                                }
                            } else {

                                $inspectionTime = 'طول اليوم';
                                $avilable = true;
                            }

                            $date = Carbon::today()->format('Y-m-d');
                            $violationCount = Violation::where('point_id', $point->id)->where('status', 1)->whereDate('created_at', $date)->count();
                            $absenceCount = Absence::where('point_id', $point->id)->where('flag', 1)->whereDate('date', $date)->count();
                            $paperCount = paperTransaction::where('point_id', $point->id)->where('status', 1)->whereDate('date', $date)->count();

                            $is_visited = ($violationCount > 0 || $absenceCount > 0 || $paperCount > 0);
                            $sector = $point->sector->name;
                            $groupPointsData[] = [
                                'point_id' => $point->id,
                                'point_name' => $point->name,
                                'point_governate' => $point->government->name,
                                'point_time' => $inspectionTime,
                                'point_shift' => $pointTime,
                                'point_location' => $point->google_map,
                                'Point_availability' => $avilable,
                                'latitude' => $point->lat,
                                'longitude' => $point->long,
                                'is_visited' => $is_visited,
                                'count_violation' => $violationCount,
                                'count_absence' => $absenceCount
                            ];
                        }
                    }


                    $missionData[] = [
                        'mission_id' => $mission->id,
                        'inspector_shift' => $inspector_shift,
                        'governate' => $groupPoint->government->name,
                        'sector' => $sector,
                        'name' => $groupPoint->name,
                        'points_count' => count($groupPointsData),
                        'points' => $groupPointsData,
                        'created_at' => $mission->created_at
                    ];
                }
            }
            $instantMissionData = [];
            if (!is_null($mission->ids_instant_mission)) {
                foreach ($instantMissions as $instant) {
                    $instantmissioncount++;
                    $instantmission =  instantmission::find($instant);
                    // dd( $instantmission);

                    if ($instantmission) {

                        if (str_contains($instantmission->location, 'gis.paci.gov.kw')) {
                            // dd("yes");
                            $location = null;
                            $kwFinder = $instantmission->location;
                        } else {
                            $location = $instantmission->location;
                            $kwFinder = null;
                        }


                        $createdAt = $instantmission->created_at;



                        $time = $createdAt->format('h:i'); // Only time

                        // Determine if it's AM or PM and set the Arabic equivalent
                        $period = $createdAt->format('A'); // AM or PM
                        $time_arabic = ($period === 'AM') ? 'صباحا' : 'مساءا';

                        $instantMissionData[] = [
                            'instant_mission_id' => $instantmission->id,
                            'name' => $instantmission->label,
                            'location' => $location,
                            'KWfinder' => $kwFinder,
                            'description' => $instantmission->description,
                            'group' => $instantmission->group ? $instantmission->group->name : 'N/A',  // Include group name
                            'team' => $instantmission->groupTeam ? $instantmission->groupTeam->name : 'N/A',  // Include group team name
                            'date' => $createdAt->format('Y-m-d'),
                            'time' => $time ?? null,
                            'time_name' => $time_arabic ?? null,
                            'latitude' => $instantmission->latitude,
                            'longitude' => $instantmission->longitude,
                            'attachment' => $instantmission->attachment,

                        ];
                    }
                }
            }
        }
        $count += $instantmissioncount;

        // Include the instant missions in the response
        //   $instantMissionData = [];

        /*  foreach ($instantmissions as $instantmission) {
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
        */ //
        $dayNamesArabic = [
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
            'Saturday'  => 'السبت',
        ];
        $dayName = date('l');
        $date = date('Y-m-d');
        if ($missionData) {
            $responseData = [
                'date' => $date,
                'date_name' => $dayNamesArabic[$dayName],
                'mission_count' => $count,
                'inspector_shift' => $inspector_shift,
                'instant_mission_count' => $instantmissioncount,
                'groupPointCount' => $groupPointCount,
                'missions' => $missionData,
                'instant_missions' => $instantMissionData,
            ];
        } else {
            $responseData = [
                'date' => $date,
                'date_name' => $dayNamesArabic[$dayName],
                // 'date' => $dayNamesArabic[$dayName] . ', ' . $date,
                'mission_count' => 0,
                'inspector_shift' => null,

                'instant_mission_count' => $instantmissioncount,
                'groupPointCount' => 0,
                'missions' => null,
                'instant_missions' => $instantMissionData,
            ];
        }

        // $success['ViolationType'] = $missionData->map(function ($item) {
        //     return $item->only(['id', 'name']);
        // });
        // return response()->json($missionData);
        return $this->respondSuccess($responseData, 'Get Data successfully.');
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
            //dd($request->id);

            $attendanceCount = $request->has('AtendanceEmployee') ? count($request->input('AtendanceEmployee')) : 0;

            $attendance = new Attendance();
            $attendance->date = Carbon::now()->format('Y-m-d');
            // $attendance->mission_id = $request->mission_id ?? null;
            $attendance->instant_id = $request->instant_mission_id;
            $attendance->total = $attendanceCount;
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
                $attendanceCount = $request->has('AtendanceEmployee') ? count($request->input('AtendanceEmployee')) : 0;

                $attendance = new Attendance();
                $attendance->date = Carbon::now()->format('Y-m-d');
                //$attendance->mission_id = $request->mission_id;
                $attendance->instant_id = $request->instant_mission_id;
                $attendance->total = $attendanceCount;
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
                $attendanceCount = $request->has('AtendanceEmployee') ? count($request->input('AtendanceEmployee')) : 0;

                $attendance = new Attendance();
                $attendance->date = Carbon::now()->format('Y-m-d');
                //$attendance->mission_id = $request->mission_id;
                $attendance->instant_id = $request->instant_mission_id;
                $attendance->total = $attendanceCount;
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
                'total_force' => $attendanceEmployees->count(),
                'total_police' => $attendanceEmployees->where('type_id', 2)->count(),
                'total_individuals' => $attendanceEmployees->where('type_id', 1)->count(),
                'total_workers' => $attendanceEmployees->where('type_id', 3)->count(),
                'total_civilian' => $attendanceEmployees->where('type_id', 4)->count(),
                'force_names' => $attendanceEmployees->map(function ($emp, $index) {
                    return [
                        'index' => $index + 1,
                        'name' => $emp->name,
                        'type' => $emp->type->name,
                        'type_id' => $emp->type_id,
                        'force_id' => $emp->force_id,
                        'force_name' => $emp->force ? $emp->force->name : 'Unknown',
                        'grade' => $emp->grade_id ? $emp->grade->name : '',
                        'grade_id' => $emp->grade_id,
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
