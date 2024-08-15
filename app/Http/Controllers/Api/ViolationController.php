<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\grade;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationTypes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ViolationController  extends Controller
{
    //

   
    public function get_Violation_type(Request $request)
    {
        $type = $request->type;
        $allViolationType = ViolationTypes::whereJsonContains('type_id', $type)->get();
        if ($allViolationType->isNotEmpty()) {
            $grade = grade::all();
            if ($grade->isNotEmpty()) {
                $success['grade'] = $grade->map(function ($item) {
                    return $item->only(['id', 'name']);
                });
            } else {
                $success['grade'] = "لا يوجد بيانات";
            }
            $success['ViolationType'] = $allViolationType->map(function ($item) {
                return $item->only(['id', 'name']);
            });
            return $this->respondSuccess($success, 'Get Data successfully.');
        } else {
            return $this->respondError('type not found', ['error' => 'خطأ فى استرجاع البيانات'], 404);
        }

        // 
    }

    public function add_Violation(Request $request)
    {
        $messages = [
            'type.required' => 'type required',
        ];
        $validatedData = Validator::make($request->all(), [
            'type' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        if ($request->type == "1") {
            // dd($request);
            $messages = [
                'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
                'name.string' => 'الاسم  يجب أن يكون نصاً.',
                'military_number.required' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                'grade.required' => 'الرتبة  مطلوب ولا يمكن تركه فارغاً.',
               'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
                'Civil_number.required' => 'رقم المدنى مطلوب ولا يمكن تركه فارغاً   .',
                'point_id.required' => 'رقم النقطة  مطلوب',
                'mission_id.required' => 'رقم المهمة  مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                'military_number' => 'required',
                'name' => 'required|string',
                'grade' => 'required',
                'image' => 'required',
                'violation_type' => 'required',
                'Civil_number' => 'required',
                'point_id' => 'required',
                'mission_id' => 'required',
                
            ], $messages);

            if ($validatedData->fails()) {
                return $this->respondError('Validation Error.', $validatedData->errors(), 400);
            }
             
            $idsArray = array_map('intval', explode(',', $request->violation_type));
            $cleanedString = implode(",", $idsArray);
                
            $new = new Violation();
            $new->name = $request->name;
            $new->military_number = $request->military_number;
            $new->Civil_number = $request->Civil_number;
            $new->grade = $request->grade;
            $new->mission_id = $request->mission_id;
            $new->point_id = $request->point_id;
            $new->violation_type = $cleanedString;
            $new->user_id = auth()->user()->id;
            // $new->user_id = 1;
            $new->save();

            if ($request->hasFile('image')) {
                $file = $request->image;
                $path = 'Api/images/violations';
                // foreach ($file as $image) {
                UploadFilesWithoutReal($path, 'image', $new ,$file);
                // UploadFilesIM($path, 'attachment', $new, $file);
                // }
    
            }
        } 
        else {

            $messages = [    
                'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                'image' => 'required',
                'violation_type' => 'required',
            ], $messages);

            if ($validatedData->fails()) {
                return $this->respondError('Validation Error.', $validatedData->errors(), 400);
            }

            $new = new Violation();
            // $new->name = $request->name;
            // $new->military_number = $request->military_number;
            // $new->Civil_number = $request->Civil_number;
            // $new->grade = $request->grade;
            // // $new->image = $request->image;
            $new->violation_type = json_encode($request->violation_type);
            // // $new->user_id = auth()->user()->id;
            $new->user_id = 1;
            $new->save();

            if ($request->hasFile('image')) {
                $file = $request->image;
                $path = 'Api/images/violations';
                // foreach ($file as $image) {
                UploadFilesWithoutReal($path, 'image', $new ,$file);
                // UploadFilesIM($path, 'attachment', $new, $file);
                // }
    
            }
        }



        if ($new) {
            $success['violation'] = $new->only(['id', 'name', 'military_number', 'Civil_number', 'grade', 'image', 'violation_type', 'user_id']);
             return $this->respondSuccess($success, 'Data Saved successfully.');
        } else {
            return $this->respondError('failed to save', ['error' => 'خطأ فى حفظ البيانات'], 404);
        }
    }
}
