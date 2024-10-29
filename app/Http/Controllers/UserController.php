<?php

namespace App\Http\Controllers;

use App\Models\Government;
use Carbon\Carbon;
use App\Models\job;
use App\Models\Rule;
use App\Models\User;
// use Illuminate\Validation\Rule;
use App\Models\grade;
use Illuminate\Support\Str;
use App\Models\departements;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Rules\UniqueNumberInUser;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Validation\Rule as ValidationRule;
use App\helper; // Adjust this namespace as per your helper file location
use App\Models\Country;
use App\Models\Qualification;
use App\Models\Region;
use App\Models\Sector;
use App\Models\Statistic;
use App\Models\UserStatistic;

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
        // if()
        return view('user.view', compact('id'));
    }

    public function getUsers($id)
    {


        $flagType = $id == 0 ? 'user' : 'employee';
        $parentDepartment = Departements::find(Auth()->user()->department_id);

        // dd(Auth::user()->rule->name);
        if (Auth::user()->rule->name == "localworkadmin") {

            $data = User::where('flag', $flagType)->get();
            // dd($data);
        } elseif (Auth::user()->rule->name == "superadmin") {
            if ($flagType == 'employee') {
                $data = User::where('flag', $flagType)->get();
            } else {
                $data = User::where('flag', $flagType)
                    ->whereHas('rule', function ($query) {
                        $query->where('hidden', false);
                    })->get();
            }
        } else {
            if (is_null($parentDepartment->parent_id)) {
                $subdepart = Departements::where('parent_id', $parentDepartment->id)->pluck('id')->toArray();

                if ($flagType == 'employee') {
                    $data = User::where('flag', $flagType)
                        ->where(function ($query) use ($subdepart, $parentDepartment) {
                            $query->whereIn('department_id', $subdepart)
                                ->orWhere('department_id', $parentDepartment->id)
                                ->orwhereNull('department_id');
                        })
                        // ->whereIn('department_id', $subdepart)
                        // ->orWhere('department_id', $parentDepartment->id)
                        ->get();
                } else {
                    $data = User::where('flag', $flagType)
                        ->where(function ($query) use ($subdepart, $parentDepartment) {
                            $query->whereIn('department_id', $subdepart)
                                ->orWhere('department_id', $parentDepartment->id);
                        })
                        ->whereHas('rule', function ($query) {
                            $query->where('hidden', false);
                        })
                        // ->whereIn('department_id', $subdepart)
                        // ->orWhere('department_id', $parentDepartment->id)
                        ->get();
                }
            } else {
                $childDepartmentIds = $parentDepartment->getAllChildren()->pluck('id');

                if ($flagType == 'employee') {

                    $data = User::where('flag', $flagType)
                        ->where(function ($query) use ($parentDepartment, $childDepartmentIds) {
                            $query->where('department_id', $parentDepartment->id)
                                ->orWhereIn('department_id', $childDepartmentIds);
                        })
                        ->get();
                } else {
                    $data = User::where('flag', $flagType)
                    ->where(function ($query) use ($parentDepartment, $childDepartmentIds) {
                        $query->where('department_id', $parentDepartment->id)
                              ->orWhereIn('department_id', $childDepartmentIds);
                    })
                    ->whereHas('rule', function ($query) {
                        $query->where('hidden', false);
                    })
                    ->get();
                }
            }
        }

        foreach ($data as $user) {
            $user->has_inspector_record = $user->inspectors()->exists(); // Assuming 'inspector' is a relationship on 'User'
        }

        return DataTables::of($data)
        ->addColumn('action', function ($row) {
            // Use has_inspector_record to conditionally show "Unassigned" button
            $useredit = route('user.edit', $row->id);
            $usershow = route('user.show', $row->id);
            $unsigned = route('user.unsigned', $row->id);
            $visibility = $row->department_id !== null ? 'd-block-inline' : 'd-none';
            $unassignedButton = !$row->has_inspector_record ?
                "<a href='{$unsigned}' class='btn btn-sm {$visibility}' style='background-color: #28a39c;'>
                    <i class='fa-solid fa-user-minus'></i> الغاء التعيين
                </a>" : '';

            return "
                <a href='{$usershow}' class='btn btn-sm' style='background-color: #274373;'>
                    <i class='fa fa-eye'></i> عرض
                </a>
                <a href='{$useredit}' class='btn btn-sm' style='background-color: #F7AF15;'>
                    <i class='fa fa-edit'></i> تعديل
                </a>
                {$unassignedButton}
            ";
        })
        ->addColumn('department', function ($row) {
            $department = Departements::where('id', $row->department_id)->pluck('name')->first();
            return $department;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    // public function login(Request $request)
    // {
    //     $messages = [
    //         'military_number.required' => 'رقم العسكري مطلوب.',
    //         'password.required' => 'كلمة المرور مطلوبة.',
    //     ];

    //     $validatedData = Validator::make($request->all(), [
    //         'military_number' => 'required|string',
    //         'password' => 'required|string',
    //     ], $messages);

    //     if ($validatedData->fails()) {
    //         return back()->withErrors($validatedData)->withInput();
    //     }

    //     $military_number = $request->military_number;
    //     $password = $request->password;

    //     // Check if the user exists
    //     $user = User::where('military_number', $military_number)->first();

    //     if (!$user) {
    //         return back()->with('error', 'الرقم العسكري لا يتطابق مع سجلاتنا');
    //     }

    //     // Check if the user has the correct flag
    //     if ($user->flag !== 'user') {
    //         return back()->with('error', 'لا يسمح لك بدخول الهيئة');
    //     }

    //     $credentials = $request->only('military_number', 'password');

    //     // Check if the user has logged in within the last two hours
    //     $twoHoursAgo = now()->subHours(6);

    //     if (Auth::attempt($credentials)) {
    //         // If the user has logged in within the last two hours, do not set the code
    //         if ($user->updated_at >= $twoHoursAgo) {

    //             $firstlogin = 0;
    //             if ($user->token == null) {
    //                 $firstlogin = 1;
    //                             $set = '123456789';
    //         $code = substr(str_shuffle($set), 0, 4);

    //         $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;

    //         $response = send_sms_code($msg, $user->phone, $user->country_code);
    //         $result = json_decode($response, true);

    //         if (isset($result['sent']) && $result['sent'] === 'true') {
    //             return view('verfication_code', compact('code', 'military_number', 'password'));
    //         } else {
    //             return back()->with('error', 'سجل الدخول مرة أخرى');
    //         }
    //                // return view('resetpassword', compact('military_number', 'firstlogin'));
    //             }

    //             Auth::login($user); // Log the user in
    //             return redirect()->route('home');
    //         }

    //         $set = '123456789';
    //         $code = substr(str_shuffle($set), 0, 4);

    //         $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;

    //         $response = send_sms_code($msg, $user->phone, $user->country_code);
    //         $result = json_decode($response, true);

    //         if (isset($result['sent']) && $result['sent'] === 'true') {
    //             return view('verfication_code', compact('code', 'military_number', 'password'));
    //         } else {
    //             return back()->with('error', 'سجل الدخول مرة أخرى');
    //         }
    //     }

    //     return back()->with('error', 'كلمة المرور لا تتطابق مع سجلاتنا');
    // }
    public function login(Request $request)
    {
        $messages = [
            'number.required' => 'رقم العسكري مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ];

        $validatedData = $request->validate([
            'number' => 'required|string',
            'password' => 'required|string',
        ], $messages);

        $number = $request->number;
        $password = $request->password;

        // Check if the user exists
        $user = User::where('military_number', $number)->orwhere('Civil_number', $number)->first();
        if (!$user) {
            return back()->with('error', 'الرقم العسكري / الرقم المدنى لا يتطابق مع سجلاتنا')->withInput();
        }

        // Check if the user has the correct flag
        if ($user->flag !== 'user') {
            return back()->with('error', 'لا يسمح لك بدخول الهيئة')->withInput();
        }
        // $credentials = $request->only('number', 'password');
        $credentials = [
            'password' => $password
        ];
        // Use a custom login function
        if ($user->military_number === $number) {
            $credentials['military_number'] = $number;
        } else {
            $credentials['civil_number'] = $number;
        }
        $twoHoursAgo = now()->subHours(6);

        if (Auth::attempt($credentials)) {
            // to not send code
            if ($user->token == 'logined') {
                Auth::login($user); // Log the user in
                return redirect()->route('home');
            }
            //end code
            // if ($user->updated_at >= $twoHoursAgo) {
            //     if ($user->token == null) {
            //         $firstlogin = 1;

            //         $set = '123456789';
            //         $code = substr(str_shuffle($set), 0, 4);

            //         $msg = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;
            //         $response = send_sms_code($msg, $user->phone, $user->country_code);
            //         $result = json_decode($response, true);

            //         // if (isset($result['sent']) && $result['sent'] === 'true') {
            //         //     return view('verfication_code', compact('code', 'military_number', 'password'));
            //         // } else {
            //         //     return back()->with('error', 'سجل الدخول مرة أخرى')->withInput();
            //         // }
            //     }
            // }

            Auth::login($user); // Log the user in
            return redirect()->route('home');
        }

        return back()->with('error', 'كلمة المرور لا تتطابق مع سجلاتنا')->withInput();
    }

    public function resend_code(Request $request)
    {
        // dd($request);
        $set = '123456789';
        $code = substr(str_shuffle($set), 0, 4);
        // $msg = trans('message.please verified your account') . "\n" . trans('message.code activation') . "\n" . $code;
        $msg  = "يرجى التحقق من حسابك\nتفعيل الكود\n" . $code;
        $user = User::where('military_number', $request->number)->orwhere('Civil_number', $request->number)->first();
        // Send activation code via WhatsApp (assuming this is your preferred method)
        $response = send_sms_code($msg, $user->phone, $user->country_code);
        $result = json_decode($response, true);
        // $code = $request->code;
        $military_number = $request->military_number;
        $number = $request->number;
        $password = $request->password;
        $sent = $result['sent'];
        if ($sent === 'true') {
            // dd("true");
            return  view('verfication_code', compact('code', 'number', 'military_number', 'password'));
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
            'verfication_code.required' => 'كود التفعيل مطلوب.',
        ]);

        // Check if validation fails
        if ($validatedData->fails()) {
            return view('verfication_code')->withErrors($validatedData)
                ->with('code', $request->code)
                ->with('number', $request->number)
                ->with('password', $request->password);
        }

        $code = $request->code;
        $number = $request->number;
        $password = $request->password;

        // Check if the provided verification code matches the expected code
        if ($request->code === $request->verfication_code) {
            // Find the user by military number
            $user = User::where('military_number', $number)->orwhere('Civil_number', $number)->first();

            // Save the activation code and password
            $user->code = $request->code;
            $user->save();


            // dd($user);
            $firstlogin = 0;

            // Coming from forget_password2
            if ($user->token == null) {
                $firstlogin = 1;
                return view('resetpassword', compact('number', 'firstlogin'));
                // }

            } else {
                if (url()->previous() == route('forget_password2') || url()->previous() == route('resend_code') || url()->previous() == route('verfication_code')) {
                    return view('resetpassword', compact('number', 'firstlogin'));
                } else {
                    return redirect()->route('home');
                }
            }
        } else {
            // If verification code does not match, return back with error message and input values
            return view('verfication_code')->withErrors('الكود خاطئ.')
                ->with('code', $code)
                ->with('number', $number)
                ->with('password', $password);
        }
    }


    public function forget_password2(Request $request)
    {
        // dd($request);
        $messages = [
            'number.required' => 'رقم العسكري /الرقم المدنى مطلوب.',
        ];

        $validatedData = Validator::make($request->all(), [
            'number' => 'required|string',
        ], $messages);

        if ($validatedData->fails()) {
            return back()->withErrors($validatedData)->withInput();
        }

        $user = User::where('military_number', $request->number)->orwhere('Civil_number', $request->number)->first();

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
            $user = User::where('military_number', $request->number)->orwhere('Civil_number', $request->number)->first();
            // Send activation code via WhatsApp (assuming this is your preferred method)
            $response = send_sms_code($msg, $user->phone, $user->country_code);
            $result = json_decode($response, true);
            // $code = $request->code;
            $number = $request->number;
            $password = $request->password;
            $sent = $result['sent'];
            if ($sent === 'true') {

                return  view('verfication_code', compact('code', 'number', 'password'));
            } else {

                return back()->with('error', 'سجل الدخول مرة أخرى');
            }
        }
    }

    public function reset_password(Request $request)
    {
        $messages = [
            'number.required' => 'رقم العسكري مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password_confirm.same' => 'تأكيد كلمة المرور يجب أن يتطابق مع كلمة المرور.',
        ];

        $validatedData = Validator::make($request->all(), [
            'number' => 'required|string',
            'password' => 'required|string',
            'password_confirm' => 'same:password',
        ], $messages);

        if ($validatedData->fails()) {
            return view('resetpassword')
                ->withErrors($validatedData)
                ->with('number', $request->number)
                ->with('firstlogin', $request->firstlogin);
        }

        $user = User::where('military_number', $request->number)->orwhere('Civil_number', $request->number)->first();

        if (!$user) {
            return back()->with('error', 'الرقم العسكري المقدم لا يتطابق مع سجلاتنا');
        }
        if (Hash::check($request->password, $user->password) == true) {
            return view('resetpassword')
                ->withErrors('لا يمكن أن تكون كلمة المرور الجديدة هي نفس كلمة المرور الحالية')
                ->with('number', $request->number)
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
        $rule = Rule::where('hidden', '!=', "1")->get();
        $flag = $id;
        $grade = grade::all();
        $job = job::all();
        $govermnent = Government::all();
        $area = Region::all();
        $nationality = Country::all();
        $sector = Sector::all();
        $qualifications = Qualification::all();
        // dd($user->department_id);
        // if ($flag == "0") {
        //     $alldepartment = departements::where('id', $user->department_id)->orwhere('parent_id', $user->department_id)->get();
        // } else {
        //     $alldepartment = departements::where('id', $user->public_administration)->orwhere('parent_id', $user->public_administration)->get();
        // }

        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $alluser = User::where('flag', 'employee')->get();
        } else {
            $alluser = User::where('flag', 'employee')
                ->leftJoin('departements', 'departements.id', '=', 'users.department_id') // Use leftJoin to handle `department_id = null`
                ->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id) // Match user’s department
                        ->orWhere('departements.parent_id', Auth::user()->department_id) // Match department’s parent ID
                        ->orWhereNull('users.department_id'); // Include users without a department
                })
                ->select('users.*') // Ensure only `users` columns are selected
                ->get();
        }

        if ($user->department_id == "NULL") {
            $department = departements::all();
        } else {
            if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
                $alldepartment = departements::all();
            } else {
                $alldepartment = departements::where('id', $user->department_id)->orwhere('parent_id', $user->department_id)->get();
            }
        }
        // $alluser = User::where('department_id',$user->department_id)->where('flag','employee')->get();

        // $speificUsers = User::where('department_id',$user->department_id)->where('flag','employee')->get();
        // $permission_ids = explode(',', $rule_permisssion->permission_ids);
        // $allPermission = Permission::whereIn('id', $permission_ids)->get();
        // dd($allPermission);
        // $alldepartment = $user->createdDepartments;
        // return view('role.create',compact('allPermission','alldepartment'));
        return view('user.create', compact('alldepartment', 'rule', 'flag', 'grade', 'job', 'alluser', 'govermnent', 'area', 'nationality', 'sector', 'qualifications'));
    }

    public function unsigned($id)
    {
        //
        $user = User::find($id);
        $log = DB::table('user_departments')->insert([
            'user_id' => $user->id,
            'department_id' => $user->department_id,
            'flag' => "0",
            'created_at' => now(),
        ]);
        $user = User::find($id);
        $user->department_id  = Null;
        $user->rule_id  = Null;
        $user->password  = Null;
        $user->flag  = 'employee';

        $user->save();
        // $id = 1;
        // $unsigned = Departements::where('manger', $id)->first();

        // if ($unsigned) {
        //     $unsigned->manger = null;
        //     $unsigned->save();
        // }
        return redirect()->back()->with('success', 'تم الغاء تعيين الموظف بنجاح');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // validation

        if ($request->type == "0") {
            $messages = [
                'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
                'rule_id.required' => ' المهام  مطلوب ولا يمكن تركه فارغاً.',
                'password.required' => ' الباسورد مطلوب ولا يمكن تركه فارغاً.',
                // Add more custom messages here
            ];

            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string',
                'rule_id' => 'required',
                'password' => 'required',
            ], $messages);
        } else {
            $messages = [
                'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
                'name.string' => 'الاسم  يجب أن يكون نصاً.',
                'military_number.required_if' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
                'military_number.unique' => 'رقم العسكري الذي أدخلته موجود بالفعل.',
                'Civil_number.unique' => 'رقم المدنى الذي أدخلته موجود بالفعل.',
                // 'file_number.unique' => 'رقم الملف الذي أدخلته موجود بالفعل.',
                'phone.required' => 'رقم الهاتف مطلوب ولا يمكن تركه فارغاً.',
                'phone.unique' => 'رقم الهاتف الذي أدخلته موجود بالفعل.',
                'phone.max' => 'رقم الهاتف اقل من 6 اراقام',

                // 'file_number.required' => 'رقم الملف مطلوب ولا يمكن تركه فارغاً.',
                'Civil_number.required' => 'رقم المدنى مطلوب ولا يمكن تركه فارغاً   .',
                // 'department_id.required' => 'القسم  يجب أن يكون نصاً.',
                // Add more custom messages here
            ];

            $rules = [
                'phone' => [
                    'required',
                    'max:8',
                    ValidationRule::unique('users', 'phone'),
                ],
                'name' => 'required|string',
                // 'department_id' => 'required',
                'Civil_number' => [
                    'max:12',
                    ValidationRule::unique('users', 'Civil_number'),
                ],


                /*   'military_number' => [
                   'required_if:type_military,police',
                ], */
            ];
            /* 'file_number' => [
                    ValidationRule::unique('users', 'file_number'),
                ],*/
            if ($request->has('type_military') && $request->type_military == "police") {
                // dd("dd");
                if ($request->has('military_number')) {
                    $rules['military_number'] = [
                        'required_if:type_military,police',
                        'string',
                        'max:255',
                        ValidationRule::unique('users', 'military_number'),
                    ];
                }
                /*   if ($request->has('file_number')) {
                    $rules['file_number'] = [
                        'required_if:type_military,police',
                        'string',
                        'max:255',
                        ValidationRule::unique('users', 'file_number'),
                    ];
                } */
            }


            if ($request->has('Civil_number')) {
                $rules['Civil_number'] = [
                    'required',
                    'string',
                    'max:255',
                    ValidationRule::unique('users', 'Civil_number'),
                ];
            }

            if ($request->has('file_number')  && $request->type_military == "police") {
                $rules['file_number'] = [
                    'required',
                    'string',
                    'max:255',
                    ValidationRule::unique('users', 'file_number'),
                ];
            }


            $validatedData = Validator::make($request->all(), $rules, $messages);
        }


        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }


        if ($request->type == "0") {
            $newUser = User::find($request->name);
            // if ($newUser->department_id == null) {
            //     return redirect()->back()->withErrors(['يجب اختيار ادارة للمستخدم اولا'])->withInput();
            // }
            $newUser->department_id = 1;
            $newUser->password = Hash::make($request->password);
            $newUser->flag = "user";
            $newUser->rule_id = $request->rule_id;
            $newUser->save();
            $id = $request->type;
            return redirect()->route('user.index', ['id' => $id]);
        } else {


            $newUser = new User();
            $newUser->name = $request->name;
            $newUser->email = $request->email;
            $newUser->type = $request->gender;
            $newUser->address1 = $request->address_1;
            $newUser->address2 = $request->address_2;
            $newUser->Provinces = $request->Provinces;
            $newUser->sector = $request->sector;
            $newUser->region = $request->region;
            $newUser->military_number = $request->military_number;
            $newUser->phone = $request->phone;
            $newUser->job_title = $request->job_title;
            $newUser->nationality = $request->nationality;
            $newUser->Civil_number = $request->Civil_number;
            $newUser->seniority = $request->seniority;
            $newUser->department_id = $request->department_id;
            $newUser->public_administration = $request->department_id;
            $newUser->work_location = $request->work_location;
            $newUser->qualification = $request->qualification;
            $newUser->date_of_birth = $request->date_of_birth;
            $newUser->joining_date = $request->joining_date;
            $newUser->length_of_service = $request->end_of_service;
            $newUser->description = $request->description;
            $newUser->file_number = $request->file_number;
            $newUser->type_military = $request->type_military;
            //
            $newUser->employee_type = $request->solderORcivil;
            $newUser->flag = "employee";
            $newUser->grade_id = $request->grade_id;
            if ($request->has('job')) {
                $newUser->job_id = $request->job;
            }

            $newUser->save();
            // dd($newUser);

            if ($request->hasFile('image')) {
                $file = $request->image;
                $path = 'users/user_profile';

                UploadFilesWithoutReal($path, 'image', $newUser, $file);
            }
            session()->flash('success', 'تم الحفظ بنجاح.');

            $id = $request->type;
            return redirect()->route('user.employees', ['id' => $id]);
        }


        // if($user->flag == "user")
        // {
        //     $department = departements::where('id',$user->department_id)->orwhere('parent_id',$user->department_id)->get();
        // }
        // else
        // {
        //     $department = departements::where('id',$user->public_administration)->orwhere('parent_id',$user->public_administration)->get();
        // }
        // // $department = departements::all();
        // $hisdepartment = $user->createdDepartments;

        // return response()->json($newUser);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $user = User::find($id);
        $rule = Rule::where('hidden', '!=', "1")->get();
        $grade = grade::all();
        $joining_date = Carbon::parse($user->joining_date);
        $end_of_serviceUnit = $joining_date->addYears($user->length_of_service);
        $end_of_service = $end_of_serviceUnit->format('Y-m-d');
        $job = job::all();
        $govermnent = Government::all();
        $area = Region::all();
        $nationality = Country::all();
        $sector = Sector::all();
        $qualifications = Qualification::all();
        // dd($user);
        // if ($user->flag == "user") {
        //     $department = departements::where('id', $user->department_id)->get();
        // } else {
        //     $department = departements::where('id', $user->public_administration)->orwhere('parent_id', $user->public_administration)->get();
        // }
        // $department = departements::all();
        $department = departements::where('id', $user->department_id)->first();
        $hisdepartment = $user->createdDepartments;
        return view('user.show', compact('user', 'rule', 'grade', 'department', 'hisdepartment', 'end_of_service', 'job', 'sector', 'area', 'nationality', 'govermnent', 'qualifications'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $user = User::find($id);
        $rule = Rule::where('hidden', '!=', "1")->get();
        $grade = grade::all();
        $joining_date = Carbon::parse($user->joining_date);
        $end_of_serviceUnit = $joining_date->addYears($user->length_of_service);
        $end_of_service = $end_of_serviceUnit->format('Y-m-d');
        $job = job::all();
        $govermnent = Government::all();
        $area = Region::all();
        $nationality = Country::all();
        $sector = Sector::all();
        $qualifications = Qualification::all();
        // dd($user);
        if ($user->department_id == "NULL") {
            $department = departements::all();
        } else {
            if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
                $department = departements::all();
            } else {
                $department = departements::where('id', $user->department_id)->orwhere('parent_id', $user->department_id)->get();
            }
        }

        // $department = departements::all();
        $hisdepartment = $user->createdDepartments;
        return view('user.edit', compact('user', 'rule', 'grade', 'department', 'hisdepartment', 'end_of_service', 'job', 'sector', 'area', 'nationality', 'govermnent', 'qualifications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         //dd($request);
        $user = User::find($id);

        $military_number = $request->solderORcivil === 'military' ? $request->military_number : null;

        $messages = [
            'military_number.required_if' => 'رقم العسكري مطلوب ولا يمكن تركه فارغاً.',
            'phone.required' => 'رقم الهاتف مطلوب ولا يمكن تركه فارغاً.',
            // 'file_number.required' => 'رقم الملف مطلوب ولا يمكن تركه فارغاً.',
            'Civil_number.required' => 'رقم المدنى مطلوب ولا يمكن تركه فارغاً.',
        ];

        if ($user->flag == 'user') {
            $messages['email.required'] = 'الايميل مطلوب';
        }


        // Define validation rules
        $rules = [
            'military_number' => [
                'required_if:type_military,police',
                'max:255',
                new UniqueNumberInUser($user),
            ],
            'phone' => [
                'required',
                new UniqueNumberInUser($user),
            ],

            // 'public_administration' => 'required',
            'Civil_number' => [
                'required',
                new UniqueNumberInUser($user),
            ],
        ];
        if ($user->flag == 'user') {
            $role['email'] = [
                'required',
                new UniqueNumberInUser($user),
            ];
        }


        // Apply validation
        $validatedData = Validator::make($request->all(), $rules, $messages);
        // }

        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }


        // Update user attributes
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->address1 = $request->address_1;
        $user->address2 = $request->address_2;
        $user->description = $request->description;
        $user->military_number = $military_number;
        $user->job_title = $request->job_title;
        $user->job_id = $request->job_id;
        $user->nationality = $request->nationality;
        $user->Civil_number = $request->Civil_number;
        $user->file_number = $request->file_number;
        $user->flag = $request->flag;
        $user->job_id = $request->job;
        $user->seniority = $request->seniority;
        $user->Provinces = $request->Provinces;
        $user->sector = $request->sector;
        $user->region = $request->region;
        $user->public_administration = $request->public_administration;
        $user->department_id = $request->public_administration;
        $user->work_location = $request->work_location;
        $user->qualification = $request->qualification;
        $user->date_of_birth = $request->date_of_birth;
        $user->joining_date = $request->joining_date;
        $user->employee_type = $request->solderORcivil;
        $user->type_military = $request->type_military;
        $user->type = $request->gender;

        $user->age = Carbon::parse($request->input('date_of_birth'))->age;

        $joining_dateDate = Carbon::parse($request->input('joining_date'));
        $end_of_serviceDate = Carbon::parse($request->input('end_of_service'));
        // $user->length_of_service = $end_of_serviceDate->year - $joining_dateDate->year;
        $user->length_of_service = $request->input('end_of_service');
        if ($request->has('grade_id')) {
            $user->grade_id = $request->grade_id;
        }

        if ($request->hasFile('image')) {
            $file = $request->image;
            $path = 'users/user_profile';
            UploadFilesWithoutReal($path, 'image', $user, $file);
        } else {
            // Keep the old image if no new image is uploaded
            $user->image = $user->getOriginal('image');
        }

        if ($user->flag == "user") {
            $user->rule_id = $request->rule_id;
            if ($request->password && !Hash::check($request->password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->token = null; // Set token to null before saving
                $user->save();

                if (auth()->user()->id == $user->id) {
                    Auth::logout();
                    session()->flash('success', 'تم تغيير كلمة المرور. يرجى تسجيل الدخول مرة أخرى.');
                    return redirect('/login');
                }
            }
        }

        $user->save();
       // dd($user);
        $id = $user->flag == "user" ? "0" : "1";
        session()->flash('success', 'تم الحفظ بنجاح.');
        if ($user->flag == "user") {
            // return view('user.view', compact('id'));
            return redirect()->route('user.index', ['id' => $id]);
        } else {
            return redirect()->route('user.employees', ['id' => $id]);
        }
    }

    public function getGoverment($id)
    {
        $sector = Sector::find($id);
        $governments = Government::whereIn('id', $sector->governments_IDs)->get();
        return response()->json($governments);
    }

    public function getRegion($id)
    {

        $area = Region::where('government_id', $id)->get();
        return response()->json($area);
    }

    public function getNationality($id)
    {

        $nationality = Country::where('id', $id)->get();
        return response()->json($nationality);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function changeProfile()
    {
        $UserStatistic = UserStatistic::where('user_id', Auth::user()->id)->where('checked', 1)->pluck('statistic_id');
        $Statistics = Statistic::all();

        return view('profile.index', get_defined_vars());
    }
    public function ProfileStore(Request $request)
    {

        $statistics = $request->statistic_id;
        $UserStatistics = UserStatistic::where('user_id', Auth::user()->id)->get();
        foreach ($UserStatistics as $UserStatistic) {
            if (in_array($UserStatistic->statistic_id, $statistics)) {

                $UserStatistic->checked = 1;
            } else {
                $UserStatistic->checked = 0;
            }
            $UserStatistic->save();
        }
        session()->flash('success', 'تم الحفظ بنجاح.');
        return redirect()->back();
    }
}
