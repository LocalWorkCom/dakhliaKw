<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\grade;
use App\Models\GroupTeam;
use App\Models\InspectorMission;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Whoops\Inspector\InspectorFactory;

class UserController extends Controller
{
    //



    public function login(Request $request)
    {
        // Custom error messages
        $messages = [
            'military_number.required' => 'رقم الهوية مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ];

        // Validate request data
        $validatedData = Validator::make($request->all(), [
            'military_number' => 'required|string',
            'password' => 'required|string',
            'device_token' => 'min:2'
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $keyLogin = $request->military_number;
        $password = $request->password;

        // Retrieve user by military_number or civil_number
        $user = User::where('military_number', $keyLogin)
            ->orWhere('civil_number', $keyLogin)
            ->join('inspectors', 'user_id', '=', 'users.id')
            ->select('users.*', 'inspectors.id as inspectorId', 'inspectors.group_id' , 'inspectors.flag as flagIns' )
            ->first();

        if (!$user) {
            return $this->respondError('Validation Error.', [
                'military_number' => ['رقم الهوية لا يتطابق مع سجلات المفتشين']
            ], 400);
        }
        if ($user->flag !== 'employee' && $user->rule_id !== 4  && $user->flagIns !== 0 ) {
            return $this->respondError('Validation Error.','لا يسمح لك بدخول الهيئة', 400);
        }
        $credentials = [
            $user->military_number ? 'military_number' : 'civil_number' => $keyLogin,
            'password' => $password
        ];

        // Attempt authentication
        if (!Auth::attempt($credentials)) {
            return $this->respondError('Password Error', [
                'crediential' => ['كلمة المرور لا تتطابق مع سجلاتنا']
            ], 403);
        }

        // Generate token and update user info
        $token = $user->createToken('auth_token')->accessToken;
        $user->device_token = $request->device_token;
        $user->token = $token;
        $user->save();

        // Determine user manager status and off-day status
        $isManager = false;
        $groupTeam = GroupTeam::where('group_id', $user->group_id)
            ->whereRaw('find_in_set(?, inspector_ids)', [$user->inspectorId])
            ->first();

        if ($groupTeam && $user->inspectorId == $groupTeam->inspector_manager) {
            $isManager = true;
        }

        $today = Carbon::today()->format('Y-m-d');
        $isOff = InspectorMission::where('inspector_id', $user->inspectorId)
            ->whereDate('date', $today)
            ->value('day_off');

        $timeEdit = Setting::where('key', 'timer')->value('value');

        // Prepare success response data
        $success = [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'military_number' => $user->military_number,
                'phone' => $user->phone,
                'code' => $user->code,
                'image' => $user->image,
                'inspectorId' => $user->inspectorId,
                'isManager' => $isManager,
                'is_off' => $isOff,
                'time_edit' => $timeEdit,
                'grade' => $user->grade ? $user->grade->name : 'مدني'
            ]
        ];

        return $this->respondSuccess($success, 'User logged in successfully.');
    }




    public function reset_password(Request $request)
    {
        $messages = [
            'military_number.required' => 'رقم العسكري مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password_confirm.same' => 'تأكيد كلمة المرور يجب أن يتطابق مع كلمة المرور.',
            'password_confirm.required' => 'تأكيد كلمة المرور مطلوبة     .',
        ];

        $validatedData = Validator::make($request->all(), [
            'military_number' => 'required|string',
            'password' => 'required|string',
            'password_confirm' => 'required|same:password',
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $user = User::where('military_number', $request->military_number)->orWhere('Civil_number', $request->military_number)->first();


        if (!$user) {
            return $this->respondError('Validation Error.', ['milltary_number' => ['الرقم العسكري لا يتطابق مع سجلاتنا']], 400);
        }

        // Check if the user has the correct flag
        if ($user->flag !== 'employee') {
            return $this->respondError('Validation Error.', ['not authorized' => ['لا يسمح لك بدخول الهيئة']], 400);
        }

        if (Hash::check($request->password, $user->password) == true) {
            return $this->respondError('Validation Error.', ['password' => ['لا يمكن أن تكون كلمة المرور الجديدة هي نفس كلمة المرور الحالية']], 400);
        }
        $grade = grade::where('id', $user->grade_id)->first();
        // Update password and set token for first login if applicable
        Auth::login($user); // Log the user in
        $user->device_token = $request->device_token;
        $user->password = Hash::make($request->password);
        $user->save();
        // $success['token'] = $token;//->token;
        // $token =$user->createToken('auth_token')->accessToken;
        $success['token'] = $user->createToken('MyApp')->accessToken;
        // $user->image = $user->image;
        $userData = $user->only(['id', 'name', 'email', 'phone', 'country_code', 'code', 'image']);
        if ($grade) {
            $gradeData = ['grade' => $grade->name];
        } else {
            $gradeData = ['grade' => 'لا يوجد بيانات'];
        }

        $success['user'] = array_merge($userData, $gradeData);
        return $this->respondSuccess($success, 'reset password successfully.');
    }

    public function checkCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'military_number' => 'required_without:userId',
            'code' => 'required|min:3',
            'userId' => 'required_without:military_number',
        ]);

        if ($validator->fails()) {
            return $this->respondError('Validation Error.', $validator->errors(), 400);
        }
        $code = $request->code;
        $military_number = $request->military_number;
        $userId = $request->military_number;
        $user = User::where(function ($query) use ($military_number, $code) {
            $query->where('military_number', $military_number)->where('code', $code);
        })->orWhere([
            ['id', $userId],
            ['code', $code]
        ])->first();
        if ($user) {
            return $this->respondSuccess(json_decode('{}'), 'الكود صحيح');
        } else {
            return $this->respondError('الكود غير صحيح', ['code' => ['الكود غير صحيح']], 401);
        }
    }

    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'military_number' => 'required_without:userId',
            'userId' => 'required_without:military_number',
        ]);

        if ($validator->fails()) {
            return $this->respondError('Validation Error.', $validator->errors(), 400);
        }
        $military_number = $request->military_number;
        $user = User::where(function ($query) use ($military_number) {
            if ($military_number) {
                $query->where('military_number', $military_number);
            }
        })->orWhere('id', $request->userId)->first();
        if ($user) {
            $set = '123456789';
            $code = substr(str_shuffle($set), 0, 4);
            $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;

            send_sms_code($msg, $user->phone, $user->country_code);
            $user->code = $code;
            $user->save();
            return $this->respondSuccess(json_decode('{}'), 'تم ارسال الرسالة بنجاح');
        } else {
            return $this->respondError('user not found', ['military_number' => ['مستخدم غير مسجل لدينا']], 400);
        }
    }

    public function check_military_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'military_number' => 'required_without:userId',
        ]);

        if ($validator->fails()) {
            return $this->respondError('Validation Error.', $validator->errors(), 400);
        }

        $user = User::where('military_number', $request->military_number)->orWhere('Civil_number', $request->military_number)->join('inspectors', 'user_id', 'users.id')->first();

        if ($user) {
            // $success['user'] = $user->only(['id', 'name', 'email', 'phone', 'country_code', 'code','image']);
            // return $this->respondSuccess($success, 'reset password successfully.');
            return $this->respondSuccess(json_decode('{}'), 'الرقم العسكرى صحيح');
        } else {
            return $this->respondError('user not found', ['military_number' => ['مستخدم غير مسجل لدينا']], 400);
        }
    }

    public function logout()
    {

        Auth::user()->token()->revoke();


        return $this->respondSuccess(null, trans('تسجيل خروج'));
    }

    public function changePassword(Request $request)
    {
        // Custom validation messages
        $messages = [
            "current_password.required" => "يجب ادخال كلمه المرور الحاليه",
            "new_password.required" => "يجب ادخال كلمه المرور الجديده",
            "new_password.different" => "يجب أن تكون كلمة المرور الجديدة مختلفة عن كلمة المرور الحالية",
            "password_confirm.required" => "يجب ادخال تأكيد كلمه المرور",
            "password_confirm.same" => "يجب ان يكون كلمه المرور متطابقه"
        ];

        // Validation rules
        $validatedData = Validator::make($request->all(), [
            "current_password" => "required",
            "new_password" => "required|different:current_password",
            "password_confirm" => "required|same:new_password",
        ], $messages);

        // Check for validation failures
        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $user = Auth::user(); // Get the authenticated user

        if (!Hash::check($request->current_password, $user->password)) {
            $message=[
                'error'=>  ["كلمة المرور الحالية غير صحيحة"]
            ];
            return $this->respondError('Error.',  $message, 400);
        }

        // Update the user's password
        $user = User::find(Auth()->id());
        $user->password = Hash::make($request->new_password);
        $user->save();
        return $this->respondSuccess($user, 'Password changed successfully.');

    }
}
