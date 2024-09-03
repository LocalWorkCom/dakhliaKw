<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\grade;
use App\Models\Absence;
use App\Models\Inspector;
use App\Models\AbsenceType;
use Illuminate\Http\Request;
use App\Models\ViolationTypes;
use App\Models\AbsenceEmployee;
use App\Http\Controllers\Controller;
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
        $grade = grade::all();
        if ($grade->isNotEmpty()) {
            $success['grade'] = $grade->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['grade'] = "لا يوجد بيانات";
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
    public function store(Request $request)
    {
        // 
        $messages = [
            'total_number.required' => 'الرقم الاجمالى  مطلوب ولا يمكن تركه فارغاً.',
            'actual_number.required' => 'الرقم الفعلى  مطلوب ولا يمكن تركه فارغاً.',
            'point_id.required' => 'رقم النقطة  مطلوب .',
            'mission_id.required' => 'رقم المهمة مطلوبة',
            'type_employee.required' => 'النوع مطلوب',
            'absence_types.required' => 'حاله الغياب مطلوبه',
            'name.required'=>'الاسم مطلوب'
            // 'AbsenceEmployee.required_if' => 'التاريخ مطلوبة',
        ];
        $validatedData = Validator::make($request->all(), [
            'total_number' => 'required',
            'actual_number' => 'required',
            'point_id' => 'required',

            // 'AbsenceEmployee'=> ['required_if:total_number -actual_number ,0']
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $inspectorId = Inspector::where('user_id', auth()->user()->id)->first();
        //  dd(auth()->user()->inspectors);
        $today = Carbon::today()->toDateString();
        $abs = $request->total_number - $request->actual_number;
        if ($request->total_number != $request->actual_number && $abs !=  count($request->AbsenceEmployee)) {
            return $this->respondError('يرجى ادخال باقى الموظفين', ['absence_number' => [' عدد الموظفين  المدخل لا يتوافق مع عددهم']], 400);
        } else {
            $new = new Absence();
            $new->date =  $today;
            $new->point_id = $request->point_id;
            $new->mission_id = $request->mission_id;
            $new->total_number = $request->total_number;
            $new->actual_number = $request->actual_number;
            $new->inspector_id = $inspectorId ? $inspectorId->id : null;
            $new->save();



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

                $success['Absence'] = $new->only(['id', 'date', 'total_number', 'actual_number', 'point_id', 'mission_id']);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
