<?php

namespace App\Http\Controllers\Api;

use App\Models\AbsenceEmployee;
use App\Models\grade;
use App\Models\AbsenceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Inspector;
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
            'date.required' => 'التاريخ مطلوبة',
        ];
        $validatedData = Validator::make($request->all(), [
            'total_number' => 'required',
            'actual_number' => 'required',
            'point_id' => 'required',
            'mission_id' => 'required',
            'date'=> 'required' 
        ], $messages);
        
        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $inspectorId = Inspector::where('user_id',auth()->user()->id)->first();
        //  dd(auth()->user()->inspectors);
        $new = new Absence();
        $new->date = $request->date;
        $new->point_id = $request->point_id;
        $new->mission_id = $request->mission_id;
        $new->total_number = $request->total_number;
        $new->actual_number = $request->actual_number;
        $new->inspector_id = $inspectorId ? $inspectorId->id : null;
        $new->save();

            if($new)
            {
                if($request->has('AbsenceEmployee'))
                {
                    $array=[];
                    foreach($request->AbsenceEmployee as $item)
                    {
                        $Emp = new AbsenceEmployee();
                        $Emp->name = $item["name"];
                        $Emp->grade = $item["grade"];
                        $Emp->absence_types_id  = $item["type"];
                        $Emp->absences_id  = $new->id;
                        $Emp->save();
                        if($Emp)
                        {
                            $array[]=$Emp->only(['id','name','grade','absence_types_id']);
                        }

                    }
                }

                $success['Absence'] = $new->only(['id', 'date', 'total_number', 'actual_number', 'point_id', 'mission_id']);
                $success['AbsenceEmployee'] = $array;
                return $this->respondSuccess($success, 'Data Saved successfully.');
            }
            else
            {
                return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
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
