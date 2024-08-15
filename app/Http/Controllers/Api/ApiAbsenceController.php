<?php

namespace App\Http\Controllers\Api;

use App\Models\grade;
use App\Models\AbsenceType;
use Illuminate\Http\Request;
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
        $grade = grade::all();
        if ($grade->isNotEmpty()) {
            $success['grade'] = $grade->map(function ($item) {
                return $item->only(['id', 'name']);
            });
        } else {
            $success['grade'] = "لا يوجد بيانات";
        }
        if ($absenceType->isNotEmpty()) {
            $success['absence_Type'] = $grade->map(function ($item) {
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
            $new = new Ab();
            $new->name = $request->name;
            $new->military_number = $request->military_number;
            $new->Civil_number = $request->Civil_number;
            $new->grade = $request->grade;
            $new->mission_id = $request->mission_id;
            $new->point_id = $request->point_id;
            $new->violation_type = $cleanedString;
            $new->user_id = auth()->user()->id;
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
