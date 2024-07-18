<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;


use Illuminate\Support\Facades\Validator;
use Illuminate\Console\View\Components\Alert;
use App\helper; // Adjust this namespace as per your helper file location

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::all();
        return DataTables::of($data)->make(true);
    }
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
            return back()->withErrors($validatedData)->withInput();
        }
    
        $military_number = $request->military_number;
        $password = $request->password;
    
        // Check if the user exists
        $user = User::where('military_number', $military_number)->first();
    
        if (!$user) {
            return back()->with('error', 'الرقم العسكري لا يتطابق مع سجلاتنا');
        }
    
        // Check if the user has the correct flag
        if ($user->flag !== 'user') {
            return back()->with('error', 'لا يسمح لك بدخول الهيئة');
        }
    
        $credentials = $request->only('military_number', 'password');
        if (Auth::attempt($credentials)) {
            $set = '123456789';
            $code = substr(str_shuffle($set), 0, 4);
    
            $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;
    
            $response = send_sms_code($msg, $user->phone, $user->country_code);
            $result = json_decode($response, true);
    
            if (isset($result['sent']) && $result['sent'] === 'true') {
                return view('verfication_code', compact('code', 'military_number', 'password'));
            } else {
                return back()->with('error', 'سجل الدخول مرة أخرى');
            }
        }
    
        return back()->with('error', 'كلمة المرور لا تتطابق مع سجلاتنا');
    }
    

    public function verfication_code(Request $request)
    {
        $messages = [
            'verfication_code.required' => 'كود التفعيل مطلوب.',
        ];
        // Validate incoming request data
        $validatedData = Validator::make($request->all(), [
            'verfication_code' => 'required', // Ensure verfication_code field is required

        ], $messages);

        // Check if validation fails
        if ($validatedData->fails()) {
            return view('verfication_code')->withErrors($validatedData)
                ->with('code', $request->code)
                ->with('military_number', $request->military_number)
                ->with('password', $request->password);
        }
        $code = $request->code;
        $military_number = $request->military_number;
        $password = $request->password;
        // Check if the provided verification code matches the expected code
        if ($request->code === $request->verfication_code) {
            // Find the user by military number (assuming military_number is used for identification)
            $user = User::where('military_number', $request->military_number)->first();
            // Save the activation code to the user's record (if needed)
            $user->code = $request->code;
            $user->password = Hash::make($request->password);
            $user->save();

            $firstlogin = 0;
            if ($user->token == null) {
                $firstlogin = 1;
                return view('resetpassword', compact('military_number', 'firstlogin'));
            } else {
                return redirect()->route('welcome');
            }

            // Redirect to the welcome view or return success message

        } else {
            // If verification code does not match, return back with error message and input values
            return  view('verfication_code')->withErrors('الكود خاطئ.')
                ->with('code', $code)
                ->with('military_number', $military_number)
                ->with('password', $password);
        }
    }
    public function resend_code(Request $request)
    {
        // dd($request);
        $set = '123456789';
        $code = substr(str_shuffle($set), 0, 4);
        // $msg = trans('message.please verified your account') . "\n" . trans('message.code activation') . "\n" . $code;
        $msg  = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;
        $user = User::where('military_number', $request->military_number)->first();
        // Send activation code via WhatsApp (assuming this is your preferred method)
        $response = send_sms_code($msg, $user->phone, $user->country_code);
        $result = json_decode($response, true);
        // $code = $request->code;
        $military_number = $request->military_number;
        $password = $request->password;
        $sent = $result['sent'];
        if ($sent === 'true') {
            // dd("true");
            return  view('verfication_code', compact('code', 'military_number', 'password'));
        } else {

            return back()->with('error', 'سجل الدخول مرة أخرى');
        }
    }
    public function forget_password2(Request $request)
    {
        // dd($request);

        $messages = [
            'military_number.required' => 'رقم العسكري مطلوب.',
        ];
        // Validate incoming request data
        $validatedData = Validator::make($request->all(), [
            'military_number' => 'required|string',

        ], $messages);

        // $request->validate([
        //     'military_number' => 'required|string',
        // ]);
        //  Check if validation fails
        if ($validatedData->fails()) {
            return back()
                ->withErrors($validatedData)
                ->withInput();
        }
        // return redirect()->route('forget_password');

        $military_number = $request->military_number;
        $firstlogin = 0;
        //     // Check if the military number exists in the database
        $user = User::where('military_number', $request->military_number)->first();

        if (!$user) {
            return back()->with('error', 'الرقم العسكري لا يتطابق مع سجلاتنا');
        }
    
        // Check if the user has the correct flag
         elseif ($user->flag !== 'user') {
            return back()->with('error', 'لا يسمح لك بدخول الهيئة');
        }else {
            return view('resetpassword', compact('military_number', 'firstlogin'));
        }
        
    }

    public function reset_password(Request $request)
    {

        $messages = [
            'military_number.required' => 'رقم العسكري مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password_confirm.same' => 'تأكيد كلمة المرور يجب أن يتطابق مع كلمة المرور.',
        ];

        $military_number = $request->military_number;

        $validatedData = Validator::make($request->all(), [
            'military_number' => 'required|string',
            'password' => 'required|string',
            'password_confirm' => 'same:password',

        ], $messages);
        if ($validatedData->fails()) {
            // Alert('Fail', __('site.wrong'));
            // return view('resetpassword', compact('military_number'));
            return view('resetpassword')
                ->withErrors($validatedData)
                ->with('military_number', $request->military_number)
                ->with('firstlogin', $request->firstlogin);

            // return back();
        }
        $user = User::where('military_number', $request->military_number)->first();

        if (Hash::check($request->password, $user->password)) {
            return view('resetpassword')
                ->withErrors('لا يمكن أن تكون كلمة المرور الجديدة هي نفس كلمة المرور الحالية')
                ->with('military_number', $request->military_number)
                ->with('firstlogin', $request->firstlogin);
        }
        if ($request->firstlogin == 1) {
            // $user = User::where('military_number', $request->military_number)->first();
            $user->token = "logined";
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->route('welcome');
        } else {

            if ($user) {
                $set = '123456789';
                $code = substr(str_shuffle($set), 0, 4);
                $msg  = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;
                // $msg = trans('message.please verified your account') . "\n" . trans('message.code activation') . "\n" . $code;
                $user = User::where('military_number', $request->military_number)->first();
                // Send activation code via WhatsApp (assuming this is your preferred method)
                $response = send_sms_code($msg, $user->phone, $user->country_code);
                $result = json_decode($response, true);
                // $code = $request->code;
                $military_number = $request->military_number;
                $password = $request->password;
                $sent = $result['sent'];
                if ($sent === 'true') {
                    // dd("true");
                    return  view('verfication_code', compact('code', 'military_number', 'password'));
                } else {

                    return back()->with('error', 'سجل الدخول مرة أخرى');
                }
            } else {
                return back()->with('error', 'الرقم العسكري المقدم لا يتطابق مع سجلاتنا');
            }
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
        // return view('welcome');
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
        // dd("dd");
        // validation
        // $validatedData = $request->validate([
        //     'military_number' => 'required|string|unique:users|max:255',
        //     'phone' => 'required|unique:users|max:255',
        //     'password' => 'required|string|min:8|confirmed',
        //     'country_code' =>'required',
        // ]);

        $newUser = new User();
        $newUser->military_number = "123";
        $newUser->phone = "01114057863";
        $newUser->country_code = "+20";
        // $newUser->password = Hash::make($validatedData['password']);
        $newUser->password = Hash::make("123");
        $newUser->save();

        return response()->json($newUser);
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
