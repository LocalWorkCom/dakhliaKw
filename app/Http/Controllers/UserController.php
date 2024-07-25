<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Validation\Rule as ValidationRule;
use App\Models\Rule;
use App\Models\User;
// use Illuminate\Validation\Rule;
use App\Models\grade;
use Illuminate\Support\Str;
use App\Models\departements;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Console\View\Components\Alert;
use App\helper; // Adjust this namespace as per your helper file location
use App\Models\job;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(UsersDataTable $dataTable)
    // {
    //     $data = User::all();
    //     return DataTables::of($data)->make(true);
    //     // return $dataTable->render('user.view');



    // }
    public function index($id)
    {
        
        return view('user.view', compact('id'));
    }

    public function getUsers($id)
    {
        $flagType = $id == 0 ? 'user' : 'employee';
        $data = User::where('flag', $flagType)->get();

        return DataTables::of($data)->addColumn('action', function ($row) {

            return '<button class="btn  btn-sm" style="background-color: #259240;" > <i class="fa fa-edit"></i> </button>
              <a href="" class="btn  btn-sm" style=" background-color:#864824; " > <i class="fa-solid fa-mug-hot" ></i> </a>';
        })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function login(Request $request)
    {
        $messages = [
            'military_number.required' => '  ادخل الرقم العسكري.',
            'password.required' => '  ادخل كلمة المرور.',
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

    public function verfication_code(Request $request)
    {
        // Validate incoming request data
        $validatedData = Validator::make($request->all(), [
            'verfication_code' => 'required', // Ensure verfication_code field is required
        ], [
            'verfication_code.required' => '  ادخل كود التفعيل.',
        ]);

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
            // Find the user by military number
            $user = User::where('military_number', $request->military_number)->first();

            // Save the activation code and password
            $user->code = $request->code;
            $user->save();


            // dd($user);
            $firstlogin = 0;

            // Coming from forget_password2
            if ($user->token == null) {
                $firstlogin = 1;
                return view('resetpassword', compact('military_number', 'firstlogin'));
                // }

            } else {
                if (url()->previous() == route('forget_password2') || url()->previous() == route('resend_code') || url()->previous() == route('verfication_code')) {
                    return view('resetpassword', compact('military_number', 'firstlogin'));
                } else {
                    return redirect()->route('home');
                }
            }
        } else {
            // If verification code does not match, return back with error message and input values
            return view('verfication_code')->withErrors('الكود الذي ادخلته غير صحيح.')
                ->with('code', $code)
                ->with('military_number', $military_number)
                ->with('password', $password);
        }
    }


    public function forget_password2(Request $request)
    {
        $messages = [
            'military_number.required' => '  ادخل الرقم العسكري.',
        ];

        $validatedData = Validator::make($request->all(), [
            'military_number' => 'required|string',
        ], $messages);

        if ($validatedData->fails()) {
            return back()->withErrors($validatedData)->withInput();
        }

        $user = User::where('military_number', $request->military_number)->first();

        if (!$user) {
            return back()->with('error', 'الرقم العسكري لا يتطابق مع سجلاتنا');
        } elseif ($user->flag !== 'user') {
            return back()->with('error', 'لا يسمح لك بدخول الهيئة');
        } else {
            // Generate and send verification code
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

                return  view('verfication_code', compact('code', 'military_number', 'password'));
            } else {

                return back()->with('error', 'سجل الدخول مرة أخرى');
            }
        }
    }

    public function reset_password(Request $request)
    {
        $messages = [
            'military_number.required' => '  ادخل الرقم العسكري.',
            'password.required' => '  ادخل كلمة المرور.',
            'password_confirm.same' => 'تأكيد كلمة المرور يجب أن يتطابق مع كلمة المرور.',
        ];

        $validatedData = Validator::make($request->all(), [
            'military_number' => 'required|string',
            'password' => 'required|string',
            'password_confirm' => 'same:password',
        ], $messages);

        if ($validatedData->fails()) {
            return view('resetpassword')
                ->withErrors($validatedData)
                ->with('military_number', $request->military_number)
                ->with('firstlogin', $request->firstlogin);
        }

        $user = User::where('military_number', $request->military_number)->first();

        if (!$user) {
            return back()->with('error', 'الرقم العسكري المقدم لا يتطابق مع سجلاتنا');
        }
        if (Hash::check($request->password, $user->password) == true) {
            return view('resetpassword')
                ->withErrors('لا يمكن أن تكون كلمة المرور الجديدة هي نفس كلمة المرور الحالية')
                ->with('military_number', $request->military_number)
                ->with('firstlogin', $request->firstlogin); // Define $firstlogin here if needed
        }

        // Update password and set token for first login if applicable

        if ($request->firstlogin == 1) {
            $user->token = "logined";
        }
        $user->password = Hash::make($request->password);
        $user->save();
        Auth::login($user); // Log the user in

        return redirect()->route('home')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
        // return redirect()->route('home')->with('user', auth()->user());

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
    public function create($id)
    {
        //
        $user = User::find(Auth::user()->id);
        $rule = Rule::all();
        $flag = $id;
        $grade = grade::all();
        $job = job::all();
        // dd($user->department_id);
        if($user->flag == "user")
        {
            $alldepartment = departements::where('id',$user->department_id)->orwhere('parent_id',$user->department_id)->get();
        }
        else
        {
            $alldepartment = departements::where('id',$user->public_administration)->orwhere('parent_id',$user->public_administration)->get();
        }

        // $permission_ids = explode(',', $rule_permisssion->permission_ids);
        // $allPermission = Permission::whereIn('id', $permission_ids)->get();
        // dd($allPermission);
        // $alldepartment = $user->createdDepartments;
        // return view('role.create',compact('allPermission','alldepartment'));
        return view('user.create', compact('alldepartment', 'rule', 'flag', 'grade','job'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // validation

        if($request->type == "0")
        {
            $messages = [
                'military_number.required' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                'military_number.unique' => 'رقم العسكري الذي أدخلته موجود بالفعل.',
                'phone.required' => 'رقم الهاتف مطلوب ولا يمكن تركه فارغاً.',
                'phone.string' => 'رقم الهاتف يجب أن يكون نصاً.',

                'file_number.required' => 'رقم الملف مطلوب ولا يمكن تركه فارغاً.',
                'file_number.string' => 'رقم الملف يجب أن يكون نصاً.',
                'rule.required' => ' المهام  مطلوب ولا يمكن تركه فارغاً.',
                'password.required' => ' الباسورد مطلوب ولا يمكن تركه فارغاً.',
                'department.required' => 'القسم  يجب أن يكون نصاً.',
                // Add more custom messages here
            ];
            
            $validatedData = Validator::make($request->all(), [
                'military_number' => [
                    'required',
                    'string',
                    'max:255',
                    ValidationRule::unique('users', 'military_number'),
                ],
                'phone' => 'required|string',
                'file_number' => 'required|string',
                'rule' => 'required',
                'password' => 'required',
                'department' => 'required',
            ], $messages);
        }
        else
        {
            $messages = [
                // 'military_number.required' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                // 'military_number.unique' => 'رقم العسكري الذي أدخلته موجود بالفعل.',
                'phone.required' => 'رقم الهاتف مطلوب ولا يمكن تركه فارغاً.',
                'phone.string' => 'رقم الهاتف يجب أن يكون نصاً.',

                'file_number.required' => 'رقم الملف مطلوب ولا يمكن تركه فارغاً.',
                'file_number.string' => 'رقم الملف يجب أن يكون نصاً.',
                'department.required' => 'القسم  يجب أن يكون نصاً.',
                // Add more custom messages here
            ];
            
            $validatedData = Validator::make($request->all(), [
                // 'military_number' => [
                //     'required',
                //     'string',
                //     'max:255',
                //     ValidationRule::unique('users', 'military_number'),
                // ],
                'phone' => 'required|string',
                'file_number' => 'required|string',
                'department' => 'required',
            ], $messages);
        }
        
    
        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }


        // $validatedData = $request->validate([
        //     'military_number' => 'required|string|unique:users|max:255',
        //     'phone' => 'required|max:255',
        //     // 'password' => 'required|string|min:8|confirmed',
        //     // 'country_code' =>'required',
        // ]);

        if ($request->type == "0") {
            $newUser = new User();
            $newUser->military_number = $request->military_number;
            $newUser->phone = $request->phone;
            $newUser->country_code = "+20";
            $newUser->name = $request->name;
            $newUser->file_number = $request->file_number;
            $newUser->flag = "user";
            $newUser->rule_id = $request->rule;
            if($request->has('job'))
            {
                $newUser->job_id = $request->job;
            }
            $newUser->department_id  = $request->department;
            $newUser->password = Hash::make($request->password);
            $newUser->save();
        } else {
            $newUser = new User();
            $newUser->military_number = $request->military_number;
            $newUser->phone = $request->phone;
            $newUser->country_code = "+20";
            $newUser->name = $request->name;
            $newUser->file_number = $request->file_number;
            $newUser->flag = "employee";
            if ($request->has('solder') && $request->solder == "on") {
                $newUser->grade_id = $request->grade_id;
            }
            if($request->has('job'))
            {
                $newUser->job_id = $request->job;
            }
            // $newUser->password = NUll;
            $newUser->description = $request->description;
            // $newUser->job = $request->job;
            $newUser->date_of_birth = $request->date_of_birth;
            $newUser->public_administration = $request->department;
            // $newUser->department_id  = $request->department;
            $newUser->save();

            if ($request->hasFile('image')) {
                $file = $request->image;
                $path = 'users/user_profile';

                UploadFilesWithoutReal($path, 'image', $newUser, $file);
            }
        }

        $id = $request->type;


        // return response()->json($newUser);
        return view('user.view', compact('id'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $user = User::find($id);
        $rule = Rule::all();
        $grade = grade::all();
        $joining_date = Carbon::parse($user->joining_date);
        $end_of_serviceUnit = $joining_date->addYears($user->length_of_service);
        $end_of_service = $end_of_serviceUnit->format('Y-m-d');
        $job = job::all();
        // dd($user);
        if($user->flag == "user")
        {
            $department = departements::where('id',$user->department_id)->orwhere('parent_id',$user->department_id)->get();
        }
        else
        {
            $department = departements::where('id',$user->public_administration)->orwhere('parent_id',$user->public_administration)->get();
        }
        // $department = departements::all();
        $hisdepartment = $user->createdDepartments;
        return view('user.show', compact('user', 'rule', 'grade', 'department', 'hisdepartment', 'end_of_service' ,'job' ));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $user = User::find($id);
        $rule = Rule::all();
        $grade = grade::all();
        $joining_date = Carbon::parse($user->joining_date);
        $end_of_serviceUnit = $joining_date->addYears($user->length_of_service);
        $end_of_service = $end_of_serviceUnit->format('Y-m-d');

        $job = job::all();
        // dd($user);
        if($user->flag == "user")
        {
            $department = departements::where('id',$user->department_id)->orwhere('parent_id',$user->department_id)->get();
        }
        else
        {
            $department = departements::where('id',$user->public_administration)->orwhere('parent_id',$user->public_administration)->get();
        }
        // $department = departements::all();
        $hisdepartment = $user->createdDepartments;
        return view('user.edit', compact('user', 'rule', 'grade', 'department', 'hisdepartment', 'end_of_service' ,'job' ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if($user->flag == "user")
        {
            $messages = [
                'military_number.required' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                'phone.required' => 'رقم الهاتف مطلوب ولا يمكن تركه فارغاً.',
                'phone.string' => 'رقم الهاتف يجب أن يكون نصاً.',

                'file_number.required' => 'رقم الملف مطلوب ولا يمكن تركه فارغاً.',
                'file_number.string' => 'رقم الملف يجب أن يكون نصاً.',
                'rule_id.required' => ' المهام  مطلوب ولا يمكن تركه فارغاً.',
                'password.required' => ' الباسورد مطلوب ولا يمكن تركه فارغاً.',
                'department_id.required' => 'القسم  يجب أن يكون نصاً.',
                'Civil_number.required' => 'رقم المدنى مطلوب ولا يمكن تركه فارغاً.',

                // Add more custom messages here
            ];
            
            $validatedData = Validator::make($request->all(), [
                'military_number' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'phone' => 'required|string',
                'file_number' => 'required|string',
                'rule_id' => 'required',
                'password' => 'required',
                'department_id' => 'required',
                'Civil_number' => 'required',
                
            ], $messages);
        }
        else
        {
            $messages = [
                'military_number.required' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                'phone.required' => 'رقم الهاتف مطلوب ولا يمكن تركه فارغاً.',
                'phone.string' => 'رقم الهاتف يجب أن يكون نصاً.',

                'file_number.required' => 'رقم الملف مطلوب ولا يمكن تركه فارغاً.',
                'file_number.string' => 'رقم الملف يجب أن يكون نصاً.',
                'public_administration.required' => 'القسم  يجب أن يكون نصاً.',
                'Civil_number.required' => 'رقم المدنى مطلوب ولا يمكن تركه فارغاً.',

                // Add more custom messages here
            ];
            
            $validatedData = Validator::make($request->all(), [
                'military_number' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'phone' => 'required|string',
                'file_number' => 'required|string',
                'public_administration' => 'required',
                'Civil_number' => 'required',
                
            ], $messages);
        }
        
    
        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }


        // dd($request);
       
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->description = $request->description;
        $user->military_number = $request->military_number;
        if($request->has('job'))
        {
            $user->job_id = $request->job;
        }
        // $user->job_id = $request->job;
        $user->job_title = $request->job_title;
        $user->nationality = $request->nationality;
        $user->Civil_number = $request->Civil_number;
        $user->file_number = $request->file_number;
        $user->flag = $request->flag;
        $user->seniority = $request->seniority;
        $user->public_administration = $request->public_administration;
        $user->work_location = $request->work_location;
        // $user->position = $request->position;
        $user->qualification = $request->qualification;
        $user->date_of_birth = $request->date_of_birth;
        $user->joining_date = $request->joining_date;
        $user->age = Carbon::parse($request->input('date_of_birth'))->age;

        $joining_dateDate = Carbon::parse($request->input('joining_date'));
        $end_of_serviceDate = Carbon::parse($request->input('end_of_service'));
        $user->length_of_service =  $end_of_serviceDate->year - $joining_dateDate->year;
        if ($request->has('grade_id')) {
            $user->grade_id = $request->grade_id;
        }
        if ($request->hasFile('image')) {
            $file = $request->image;
            $path = 'users/user_profile';

            UploadFilesWithoutReal($path, 'image', $user, $file);
        }

        if ($user->flag == "user") {
            $user->rule_id = $request->rule_id;
            $user->department_id  = $request->department_id;
            $user->password = Hash::make($request->password);
        }
        $user->save();
        // dd($user);
        if ($user->flag == "user") {
            $id = "0";
        } else {
            $id = "1";
        }



        // return response()->json($newUser);
        return view('user.view', compact('id'));
        // return view('user.edit',compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}