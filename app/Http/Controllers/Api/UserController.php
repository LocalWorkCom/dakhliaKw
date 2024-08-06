<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validator->errors(), 400);
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
        $twoHoursAgo = now()->subHours(6);

        if (Auth::attempt($credentials)) {
            // If the user has logged in within the last two hours, do not set the code
            if ($user->updated_at >= $twoHoursAgo) {

             
                
                Auth::login($user); // Log the user in
                $user->device_token = $request->device_token;
                $user->save();
                $success['token'] = $user->createToken('MyApp')->accessToken;
                $user->image=$user->image;
                $success['user'] = $user->only(['id', 'firstname', 'email', 'lastname', 'phone', 'country_code', 'code','image']);
              return $this->respondSuccess($success, trans('message.User login successfully.'));

            }

          /*   $set = '123456789';
            $code = substr(str_shuffle($set), 0, 4);

            $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;

            $response = send_sms_code($msg, $user->phone, $user->country_code);
            $result = json_decode($response, true);

            if (isset($result['sent']) && $result['sent'] === 'true') {
                return view('verfication_code', compact('code', 'military_number', 'password'));
            } else {
                return back()->with('error', 'سجل الدخول مرة أخرى');
            } */
        }
        return $this->respondError('password error', ['crediential' => ['كلمة المرور لا تتطابق مع سجلاتنا']], 403);
        //return back()->with('error', 'كلمة المرور لا تتطابق مع سجلاتنا');
    }
}
