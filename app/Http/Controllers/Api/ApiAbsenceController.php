<?php

namespace App\Http\Controllers\Api;

use App\Models\InspectorMission;
use Carbon\Carbon;
use App\Models\grade;
use App\Models\Absence;
use App\Models\Inspector;
use App\Models\AbsenceType;
use Illuminate\Http\Request;
use App\Models\ViolationTypes;
use App\Models\AbsenceEmployee;
use App\Http\Controllers\Controller;
use App\Models\AbsenceViolation;
use App\Models\Point;
use App\Models\PointDays;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;

class ApiAbsenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $absenceType = AbsenceType::all();
        $type = ViolationTypes::where('type_id', '0')->get();
        $grade = grade::where('type', 0)->get();
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
        if ($absenceType->isNotEmpty()) {
            $success['absence_Type'] = $absenceType->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['absence_Type'] = "لا يوجد بيانات";
        }
        if ($type->isNotEmpty()) {
            $success['type'] = $type->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['type'] = "لا يوجد بيانات";
        }

        return $this->respondSuccess($success, 'Get Data successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function todayIndex($today)
    {
        $daysOfWeek = [

            "الأحد",
            "الاثنين",
            "الثلاثاء",
            "الأربعاء",
            "الخميس",
            "الجمعة",
            "السبت",
        ];

        $todayDate = Carbon::parse($today);
        $dayWeek = $todayDate->locale('ar')->dayName;
        $index = array_search($dayWeek, $daysOfWeek);

        return $index !== false ? $index : null;
    }
    function isTimeAvailable($pointStart, $pointEnd)
    {
        $currentTime = Carbon::now()->format('H:i');
        $start = Carbon::createFromTimeString($pointStart);
        $end = Carbon::createFromTimeString($pointEnd)->addMinutes(30);
        $current = Carbon::createFromTimeString($currentTime);

        return $current->between($start, $end);
    }
    public function store(Request $request)
    {
        //
        $messages = [
            'total_number.required' => 'الرقم الاجمالى  مطلوب ولا يمكن تركه فارغاً.',
            'police_number.required' => 'العدد الفعلي للظباط  مطلوب ولا يمكن تركه فارغاً.',
            'civilian_number.required' => 'العدد الفعلي للمدنيين مطلوب ولا يمكن تركه فارغاً.',
            'workers_number.required' => 'العدد الفعلى للمهنين مطلوب ولا يمكن تركه فارغاً.',
            'individual_number.required' => 'العدد الفعلى للافراد مطلوب ولا يمكن تركه فارغاً.',
            'point_id.required' => 'رقم النقطة  مطلوب .',
            'mission_id.required' => 'رقم المهمة مطلوبة',
            'type_employee.required' => 'النوع مطلوب',
            'absence_types.required' => 'حاله الغياب مطلوبه',
            'name.required' => 'الاسم مطلوب'
            // 'AbsenceEmployee.required_if' => 'التاريخ مطلوبة',
        ];
        $validatedData = Validator::make($request->all(), [
            'total_number' => 'required',
            'police_number' => 'required',
            'civilian_number' => 'required',
            'workers_number' => 'required',
            'individual_number' => 'required',
            'point_id' => 'required',

            // 'AbsenceEmployee'=> ['required_if:total_number -actual_number ,0']
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->format('Y-m-d');
        // Retrieve the currently authenticated user
        $inspector = Inspector::where('user_id', Auth::id())->first();
        // dd($inspectorId);
        $inspectorId = $inspector->id;
        $team_time = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->with('workingTime')
            ->get();
        // if (!checkShift() && $team_time->first()->day_off != 1) {
        //     return $this->respondError('Validation Error.', 'لا يمكن تسجيل المخالفه خارج مواعيد العمل ', 400);
        // }
        $inspectorId = Inspector::where('user_id',  auth()->user()->id)->first();
        //  dd(auth()->user()->inspectors);
        $today = Carbon::today()->toDateString();
        $police_number = $request->police_number;
        $civilian_number = $request->civilian_number;
        $workers_number = $request->workers_number;
        $individual_number = $request->individual_number;
        $actual_number = $police_number + $civilian_number + $workers_number + $individual_number;
        $abs = $request->total_number - $actual_number;
        if ($request->total_number != $actual_number && $abs !=  count($request->AbsenceEmployee)) {
            return $this->respondError('يرجى ادخال باقى الموظفين', ['absence_number' => [' عدد الموظفين  المدخل لا يتوافق مع عددهم']], 400);
        } else {
            $today = Carbon::now()->toDateString();
            $index = $this->todayIndex($today);
            $point = Point::find($request->point_id);
            if ($point && $point->work_type == 1) {
                $pointDay = $point->pointDays->where('name', $index)->first();
                $workTime = PointDays::where('point_id', $request->point_id)->where('name', $index)->first();
                $startTime = $workTime->from;
                $endtTime = $workTime->to;;
                $is_avilable = $this->isTimeAvailable($startTime, $endtTime);
                if (!$is_avilable) {
                    return $this->respondError('failed to save', ['error' => 'انتهت مواعيد عمل النقطه'], 404);
                }
            }
            $new = new Absence();
            $new->date =  $today;
            $new->point_id = $request->point_id;
            $new->mission_id = $request->mission_id;
            $new->total_number = $request->total_number;
            $new->actual_number = $actual_number;
            $new->inspector_id = $inspectorId ? $inspectorId->id : null;
            $new->save();


            $absence_violation = new AbsenceViolation();
            $absence_violation->actual_number = $individual_number;
            $absence_violation->absence_id = $new->id;
            $absence_violation->violation_type_id = 1;
            $absence_violation->save();

            $absence_violation = new AbsenceViolation();
            $absence_violation->actual_number = $police_number;
            $absence_violation->absence_id = $new->id;
            $absence_violation->violation_type_id = 2;
            $absence_violation->save();

            $absence_violation = new AbsenceViolation();
            $absence_violation->actual_number = $workers_number;
            $absence_violation->absence_id = $new->id;
            $absence_violation->violation_type_id = 3;
            $absence_violation->save();

            $absence_violation = new AbsenceViolation();
            $absence_violation->actual_number = $civilian_number;
            $absence_violation->absence_id = $new->id;
            $absence_violation->violation_type_id = 4;
            $absence_violation->save();



            if ($new) {
                $array = [];
                if ($request->has('AbsenceEmployee') && ($request->total_number > $request->actual_number)) {


                    foreach ($request->AbsenceEmployee as $item) {
                        $employeeValidator = Validator::make($item, [
                            'name' => 'required',
                            'type_employee' => 'required',
                            'absence_types' => 'required'
                        ], $messages);

                        if ($employeeValidator->fails()) {
                            return $this->respondError('Validation Error.', $employeeValidator->errors(), 400);
                        }
                        $Emp = new AbsenceEmployee();
                        $Emp->name = $item["name"];
                        $Emp->grade = $item["grade"] ?? null;
                        $Emp->military_number  = $item["military_number"] ?? null;
                        $Emp->civil_number  = $item["civil_number"] ?? null;
                        $Emp->absence_types_id  = $item["absence_types"] ?? null;
                        $Emp->file_num  = $item["file_num"] ?? null;

                        $Emp->type_employee = $item["type_employee"] ?? null;
                        $Emp->absences_id  = $new->id;
                        $Emp->save();
                        if ($Emp) {
                            $array[] = $Emp->only(['id', 'name', 'military_number', 'civil_number', 'file_num', 'grade', 'absence_types_id', 'type_employee']);
                        }
                    }
                }
                $absence_violations = AbsenceViolation::where('absence_id', $new->id)->get();

                $actual_number = []; // Initialize the array to store actual numbers

                foreach ($absence_violations as $absence_violation) {
                    $actual_number[$absence_violation->violation_type->name] = $absence_violation->actual_number;
                }

                // Merge `actual_number` into the `Absence` array
                $success['Absence'] = array_merge(
                    $new->only(['id', 'date', 'total_number', 'actual_number', 'point_id', 'mission_id']),
                    ['actual_number' => $actual_number]
                );
                $success['AbsenceEmployee'] = $array;
                return $this->respondSuccess($success, 'Data Saved successfully.');
            } else {
                return $this->respondError('failed to save', ['error' => ['خطأ فى حفظ البيانات']], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $messages = [
            'total_number.required' => 'الرقم الاجمالى  مطلوب ولا يمكن تركه فارغاً.',
            'police_number.required' => 'العدد الفعلي للظباط  مطلوب ولا يمكن تركه فارغاً.',
            'civilian_number.required' => 'العدد الفعلي للمدنيين مطلوب ولا يمكن تركه فارغاً.',
            'workers_number.required' => 'العدد الفعلى للمهنين مطلوب ولا يمكن تركه فارغاً.',
            'individual_number.required' => 'العدد الفعلى للافراد مطلوب ولا يمكن تركه فارغاً.',
            'point_id.required' => 'رقم النقطة  مطلوب .',
            'mission_id.required' => 'رقم المهمة مطلوبة',
            'type_employee.required' => 'النوع مطلوب',
            'absence_types.required' => 'حاله الغياب مطلوبه',
            'name.required' => 'الاسم مطلوب'
            // 'AbsenceEmployee.required_if' => 'التاريخ مطلوبة',
        ];
        $validatedData = Validator::make($request->all(), [
            'total_number' => 'required',
            'police_number' => 'required',
            'civilian_number' => 'required',
            'workers_number' => 'required',
            'individual_number' => 'required',
            'point_id' => 'required',

            // 'AbsenceEmployee'=> ['required_if:total_number -actual_number ,0']
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->format('Y-m-d');
        // Retrieve the currently authenticated user
        $inspector = Inspector::where('user_id', Auth::id())->first();
        // dd($inspectorId);
        $inspectorId = $inspector->id;
        $team_time = InspectorMission::whereDate('date', $today)
            ->where('inspector_id', $inspectorId)
            ->with('workingTime')
            ->get();
        // if (!checkShift() && $team_time->first()->day_off != 1) {
        //     return $this->respondError('Validation Error.', 'لا يمكن تسجيل المخالفه خارج مواعيد العمل ', 400);
        // }
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->first();
        //  dd(auth()->user()->inspectors);
        $today = Carbon::today()->toDateString();
        $police_number = $request->police_number;
        $civilian_number = $request->civilian_number;
        $workers_number = $request->workers_number;
        $individual_number = $request->individual_number;
        $actual_number = $police_number + $civilian_number + $workers_number + $individual_number;
        $abs = $request->total_number - $actual_number;
        if ($request->total_number != $actual_number && $abs !=  count($request->AbsenceEmployee)) {
            return $this->respondError('يرجى ادخال باقى الموظفين', ['absence_number' => [' عدد الموظفين  المدخل لا يتوافق مع عددهم']], 400);
        } else {
            $today = Carbon::now()->toDateString();
            $index = $this->todayIndex($today);
            $point = Point::find($request->point_id);
            if ($point && $point->work_type == 1) {
                $pointDay = $point->pointDays->where('name', $index)->first();
                $workTime = PointDays::where('point_id', $request->point_id)->where('name', $index)->first();
                $startTime = $workTime->from;
                $endtTime = $workTime->to;;
                $is_avilable = $this->isTimeAvailable($startTime, $endtTime);
                if (!$is_avilable) {
                    return $this->respondError('failed to save', ['error' => 'انتهت مواعيد عمل النقطه'], 404);
                }
            }
            $parent_absence = Absence::findOrFail($request->id);
            if ($parent_absence->inspector_id == $inspectorId->id) {

                $time_edit = Setting::where('key', 'timer')->value('value');
                $cutoffTime = $parent_absence->created_at->addMinutes($time_edit);
                if (now() > $cutoffTime) {
                    return $this->respondError('لا يمكنك تحديث هذا السجل بعد الوقت المحدد', [], 403);
                } else {
                    $parent_id = $parent_absence->parent;
                    if ($parent_id == 0) {
                        $parent_absence->flag = 0;
                        $parent_absence->save();
                        $new = new Absence();
                        $new->date =  $today;
                        $new->point_id = $request->point_id;
                        $new->mission_id = $request->mission_id;
                        $new->total_number = $request->total_number;
                        $new->actual_number = $actual_number;
                        $new->flag = 1;
                        $new->parent = $request->id;
                        $new->inspector_id = $inspectorId ? $inspectorId->id : null;
                        $new->save();
                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $individual_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 1;
                        $absence_violation->save();

                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $police_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 2;
                        $absence_violation->save();

                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $workers_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 3;
                        $absence_violation->save();

                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $civilian_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 4;
                        $absence_violation->save();

                        if ($new) {
                            $array = [];
                            if ($request->has('AbsenceEmployee') && ($request->total_number > $actual_number)) {
                                foreach ($request->AbsenceEmployee as $item) {
                                    $employeeValidator = Validator::make($item, [
                                        'name' => 'required',
                                        'type_employee' => 'required',
                                        'absence_types' => 'required'
                                    ], $messages);

                                    if ($employeeValidator->fails()) {
                                        return $this->respondError('Validation Error.', $employeeValidator->errors(), 400);
                                    }
                                    $Emp = new AbsenceEmployee();
                                    $Emp->name = $item["name"];
                                    $Emp->grade = $item["grade"] ?? null;
                                    $Emp->military_number  = $item["military_number"] ?? null;
                                    $Emp->civil_number  = $item["civil_number"] ?? null;
                                    $Emp->absence_types_id  = $item["absence_types"] ?? null;
                                    $Emp->file_num  = $item["file_num"] ?? null;
                                    $Emp->type_employee = $item["type_employee"] ?? null;
                                    $Emp->absences_id  = $new->id;
                                    $Emp->save();
                                    if ($Emp) {
                                        $array[] = $Emp->only(['id', 'name', 'military_number', 'civil_number', 'file_num', 'grade', 'absence_types_id', 'type_employee']);
                                    }
                                }
                            }
                            $created = Absence::find($request->id);
                            // dd($created->created_at ,$new->created_at );

                            $success['Absence'] = $new->only(['id', 'date', 'total_number', 'actual_number', 'point_id', 'mission_id']);
                            $success['Absence']['created_at'] = $created->created_at;
                            $success['AbsenceEmployee'] = $array;
                            return $this->respondSuccess($success, 'Data Saved successfully.');
                        } else {
                            return $this->respondError('failed to save', ['error' => ['خطأ فى حفظ البيانات']], 400);
                        }
                    } else {
                        $abcenses = Absence::where('point_id', $request->point_id)->where('parent', $parent_id)->pluck('id')->toArray();
                        foreach ($abcenses as $abcenses) {
                            $viloate = Absence::findOrFail($abcenses);
                            $viloate->flag = 0;
                            $viloate->save();
                        }
                        $new = new Absence();
                        $new->date =  $today;
                        $new->point_id = $request->point_id;
                        $new->mission_id = $request->mission_id;
                        $new->total_number = $request->total_number;
                        $new->actual_number = $actual_number;
                        $new->flag = 1;
                        $new->parent = $parent_id;
                        $new->inspector_id = $inspectorId ? $inspectorId->id : null;
                        $new->save();
                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $individual_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 1;
                        $absence_violation->save();

                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $police_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 2;
                        $absence_violation->save();

                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $workers_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 3;
                        $absence_violation->save();

                        $absence_violation = new AbsenceViolation();
                        $absence_violation->actual_number = $civilian_number;
                        $absence_violation->absence_id = $new->id;
                        $absence_violation->violation_type_id = 4;
                        $absence_violation->save();

                        if ($new) {
                            $array = [];
                            if ($request->has('AbsenceEmployee') && ($request->total_number > $request->actual_number)) {
                                foreach ($request->AbsenceEmployee as $item) {
                                    $employeeValidator = Validator::make($item, [
                                        'name' => 'required',
                                        'type_employee' => 'required',
                                        'absence_types' => 'required'
                                    ], $messages);

                                    if ($employeeValidator->fails()) {
                                        return $this->respondError('Validation Error.', $employeeValidator->errors(), 400);
                                    }
                                    $Emp = new AbsenceEmployee();
                                    $Emp->name = $item["name"];
                                    $Emp->grade = $item["grade"] ?? null;
                                    $Emp->military_number  = $item["military_number"] ?? null;
                                    $Emp->civil_number  = $item["civil_number"] ?? null;
                                    $Emp->absence_types_id  = $item["absence_types"] ?? null;
                                    $Emp->file_num  = $item["file_num"] ?? null;
                                    $Emp->type_employee = $item["type_employee"] ?? null;
                                    $Emp->absences_id  = $new->id;
                                    $Emp->save();
                                    if ($Emp) {
                                        $array[] = $Emp->only(['id', 'name', 'military_number', 'civil_number', 'file_num', 'grade', 'absence_types_id', 'type_employee']);
                                    }
                                }
                            }
                            $created = Absence::find($parent_id);
                            // dd($created->created_at ,$new->created_at );
                            $success['Absence'] = $new->only(['id', 'date', 'total_number', 'actual_number', 'point_id', 'mission_id']);
                            $success['Absence']['created_at'] = $created->created_at;

                            $success['AbsenceEmployee'] = $array;
                            return $this->respondSuccess($success, 'Data Saved successfully.');
                        } else {
                            return $this->respondError('failed to save', ['error' => ['خطأ فى حفظ البيانات']], 400);
                        }
                    }
                }
            } else {
                return $this->respondError('عفوا غير مسموح لك بالتعديل على هذه المخالفه', [], 403);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
