<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\grade;
use App\Models\Violation;
use Illuminate\Http\Request;
use App\Models\ViolationTypes;
use App\Models\InspectorMission;
use App\Http\Controllers\Controller;
use App\Models\instantmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ViolationController  extends Controller
{
    //


    public function get_Violation_type(Request $request)
    {
        // type_id : department_id
        $messages = [
            'type.required' => 'type required',
        ];
        $validatedData = Validator::make($request->all(), [
            'type' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $type = $request->type;
        $allViolationType = ViolationTypes::whereJsonContains('type_id', $type)->get();
        if ($allViolationType->isNotEmpty()) {
            $type = ViolationTypes::where('type_id', '0')->get();
            $grade = grade::all();
            if ($grade->isNotEmpty()) {
                $success['grade'] = $grade->map(function ($item) {
                    return $item->only(['id', 'name']);
                });
            } else {
                $success['grade'] = "لا يوجد بيانات";
            }
            if ($type->isNotEmpty()) {
                $success['type'] = $type->map(function ($item) {
                    return $item->only(['id', 'name']);
                });
            } else {
                $success['type'] = "لا يوجد بيانات";
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
            'flag_instantmission.required' => 'flag instantmission required',
            'civil_military.required' => 'civil_military required [military or civil]',
        ];
        $validatedData = Validator::make($request->all(), [
            'type' => 'required',
            'flag_instantmission' => 'required',
            'civil_military' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        // 1=> this violation of instant mission
        if ($request->flag_instantmission == "1") {
            $point_id = Null;
        } else {
            $point_id = $request->point_id;
        }

        if ($request->civil_military == "military") {
            $military_number = $request->military_number;
            $grade = $request->grade;
        } else {
            $military_number = Null;
            $grade = Null;
        }
        // 1 => مخالفة سلوك
        if ($request->type == "1") {
            // dd($request);
            $messages = [
                'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
                'name.string' => 'الاسم  يجب أن يكون نصاً.',
                'military_number.required_if' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                'grade.required' => 'الرتبة  مطلوب ولا يمكن تركه فارغاً.',
                'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
                'Civil_number.required' => 'رقم المدنى مطلوب ولا يمكن تركه فارغاً   .',
                'point_id.required_if' => 'رقم النقطة  مطلوب',
                'mission_id.required' => 'رقم المهمة  مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                // 'military_number' => 'required',
                'military_number' => ['required_if:civil_military,military'],
                'name' => 'required|string',
                'grade' => ['required_if:civil_military,military'],
                'image' => 'required',
                'violation_type' => 'required',
                'Civil_number' => 'required',
                'point_id' => ['required_if:flag_instantmission,0'],
                'mission_id' => 'required',

            ], $messages);

            if ($validatedData->fails()) {
                return $this->respondError('Validation Error.', $validatedData->errors(), 400);
            }

            $idsArray = array_map('intval', explode(',', $request->violation_type));
            $cleanedString = implode(",", $idsArray);

            $new = new Violation();
            $new->name = $request->name;
            $new->military_number = $military_number;
            $new->Civil_number = $request->Civil_number;
            $new->grade = $grade;
            $new->mission_id = $request->mission_id;
            $new->point_id = $point_id;
            $new->violation_type = $cleanedString;
            $new->flag_instantmission = $request->flag_instantmission;
            $new->user_id = auth()->user()->id;
            // $new->user_id = 1;
            $new->save();

            if ($request->hasFile('image')) {
                $file = $request->image;
                $path = 'Api/images/violations';
                // foreach ($file as $image) {
                UploadFilesWithoutReal($path, 'image', $new, $file);
                // UploadFilesIM($path, 'attachment', $new, $file);
                // }

            }
        } else {

            $messages = [
                'image.required' => 'المرفقات مطلوبة',
                'violation_type.required' => 'نوع المخالفة مطلوب',
                'point_id.required_if' => 'رقم النقطة  مطلوب',
                'mission_id.required' => 'رقم المهمة  مطلوب',
            ];
            $validatedData = Validator::make($request->all(), [
                'image' => 'required',
                'violation_type' => 'required',
                // 'point_id' => 'required',
                'point_id' => ['required_if:flag_instantmission,0'],
                'mission_id' => 'required',
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
            $new->flag_instantmission = $request->flag_instantmission;
            $new->mission_id = $request->mission_id;
            $new->point_id = $point_id;
            // // $new->user_id = auth()->user()->id;
            $new->user_id = 1;
            $new->save();

            if ($request->hasFile('image')) {
                $file = $request->image;
                $path = 'Api/images/violations';
                // foreach ($file as $image) {
                UploadFilesWithoutReal($path, 'image', $new, $file);
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

    public function get_all_violation(Request $request)
    {
        $messages = [
            'point_id.required' => 'point_id required',
            'mission_id.required' => 'mission_id required',
        ];
        $validatedData = Validator::make($request->all(), [
            'point_id' => 'required',
            'mission_id' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $allviolation = Violation::where('point_id',$request->point_id)->get();
    }
    public function get_all_instantMission()
    {
        // $messages = [
        //     'inspectorId.required' => 'inspectorId required',
        // ];
        // $validatedData = Validator::make($request->all(), [
        //     'inspectorId' => 'required',
        // ], $messages);

        // if ($validatedData->fails()) {
        //     return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        // }
        $today = Carbon::today()->toDateString();
        $violation = Violation::where('user_id',Auth()->user()->id)->where('flag_instantmission',"1")->whereDate('created_at',$today)->get();
        // $allviolation = InspectorMission::where('inspector_id',$request->inspectorId)->where('date',$today)->first();
       // Extract the mission_id values from the violations.
        $missionIds = $violation->pluck('mission_id')->toArray();

        // Fetch the corresponding instant missions using the extracted mission IDs.
        $instantMissions = instantmission::whereIn('id', $missionIds)->get();
       
        // dd($instantMissions);
        $success['instantMissions'] = $instantMissions;
        $success['violation'] = $violation;
        return $this->respondSuccess($success, 'Data Saved successfully.');
    }
}
