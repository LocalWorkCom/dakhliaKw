<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //


    
    public function login(Request $request)
    {
        $messages = [
            'military_number.required' => 'رقم العسكري مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ];

        $validatedData = Validator::make($request->all(), [
            'military_number' => 'required|string',
            'password' => 'required|string',
             'device_token' => 'min:2'
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }

        $military_number = $request->military_number;
        $password = $request->password;

        // Check if the user exists
        $user = User::where('military_number', $military_number)->first();

        if (!$user) {
          return $this->respondError('Validation Error.', ['milltary_number'=> 'الرقم العسكري لا يتطابق مع سجلاتنا'], 400);
        }

        // Check if the user has the correct flag
        if ($user->flag !== 'user') {
          //  return back()->with('error', 'لا يسمح لك بدخول الهيئة');
          return $this->respondError('Validation Error.', ['not authorized'=> 'لا يسمح لك بدخول الهيئة'], 400);
        }

        $credentials = $request->only('military_number', 'password');

        // Check if the user has logged in within the last two hours
        $sixHoursAgo = now()->subHours(6);

        if (Auth::attempt($credentials)) {
            // If the user has logged in within the last two hours, do not set the code
            if ($user->updated_at >= $sixHoursAgo) {

             
                $token=$user->createToken('auth_token')->accessToken;
                Auth::login($user); // Log the user in
                $user->device_token = $request->device_token;
                $user->device_token = $token->token;

                $user->save();
                $success['token'] = $token->token;
                $user->image=$user->image;
                $success['user'] = $user->only(['id', 'firstname', 'email', 'lastname', 'phone', 'country_code', 'code','image']);
              return $this->respondSuccess($success, 'User login successfully.');

            }else {

              $set = '123456789';
              $code = substr(str_shuffle($set), 0, 4);
              $input['code'] = $code;
              $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;

              send_sms_code($msg, $user->phone, $user->country_code);
              $user->code = $code;
              $user->save();
              $success['user'] = $user->only(['id', 'firstname', 'email', 'lastname', 'phone', 'country_code', 'code','image']);
              return $this->respondwarning($success, trans('message.account not verified'), ['account' => trans('message.account not verified')], 402);
          }

           
        }
        return $this->respondError('password error', ['crediential' => ['كلمة المرور لا تتطابق مع سجلاتنا']], 403);
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
        $military_number=$request->military_number;
        $userId=$request->military_number;
        $user = User::where(function ($query) use ($military_number,$code) {
            $query->where('military_number', $military_number)->where('code',$code);
        })->orWhere([
            ['id', $userId],
            ['code',$code]])->first();
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
            if($military_number){
            $query->where('military_number', $military_number);}
        })->orWhere('id', $request->userId)->first();
        if ($user) {
            $set = '123456789';
            $code = substr(str_shuffle($set), 0, 4);
            $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;

            send_sms_code($msg, $user->phone, $user->country_code);
            $user->code = $code;
            $user->save();
            return $this->respondSuccess(json_decode('{}'), 'تم ارسال الرسالة بنجاح');

        }
        
         else {
            return $this->respondError('user not found', ['error' => 'مستخدم غير مسجل لدينا'], 404);
        }
    }


    public function logout()
    {

        Auth::user()->token()->revoke();


        return $this->respondSuccess(null, trans('تسجيل خروج'));


    }

}
