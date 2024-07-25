<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\User;
use App\Models\Permission;
use App\Models\departements;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\DataTables\RoleDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRuleRequest;
use App\Http\Requests\UpdateRuleRequest;
use Illuminate\Support\Facades\Validator;

class RuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        // return $dataTable->render('role.view');
        return view('role.view');
    }
    public function getRule()
    {
        $data = Rule::all();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            
            return '<button class="btn btn-primary btn-sm">Edit</button>'
                    ;
        })
        ->addColumn('permissions', function ($row) { // New column for departments count
            $permission_ids = explode(',', $row->permission_ids);
            $allPermission = Permission::whereIn('id', $permission_ids)->pluck('name')->toArray();
            $translatedPermissions = array_map(function ($permission) {
                return __('permissions.' . $permission);
            }, $allPermission);
            return implode(', ', $translatedPermissions);
        })
        ->addColumn('department', function ($row) { // New column for departments count
        
            $department = departements::where('id', $row->department_id)->pluck('name')->first();
            return $department;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = User::find(Auth::user()->id);
        $rule_permisssion = Rule::find($user->rule_id);
        $permission_ids = explode(',', $rule_permisssion->permission_ids);
        $allPermission = Permission::whereIn('id', $permission_ids)->get();
        // dd($allPermission);
        // $alldepartment =$user->createdDepartments;
        if($user->flag == "user")
        {
            $alldepartment = departements::where('id',$user->department_id)->orwhere('parent_id',$user->department_id)->get();
        }
        else
        {
            $alldepartment = departements::where('id',$user->public_administration)->orwhere('parent_id',$user->public_administration)->get();
        }
        return view('role.create',compact('allPermission','alldepartment'));

        // return $dataTable->render('permission.create'  ,compact('models'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // dd($request);
        // $request->validate([
        //     'name' => 'required|string',
        //     'permissions_ids' => 'required',
        //     'department_id' => 'required',
        // ]);

         $messages = [
            'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
            'permissions_ids.required' => 'الصلاحية   مطلوب ولا يمكن تركه فارغاً.',

            'department_id.required' => 'الادارة  مطلوب ولا يمكن تركه فارغاً.',
            // 'name.unique' => 'رقم الهاتف يجب أن يكون نصاً.',

            // Add more custom messages here
        ];
        
        $validatedData = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                // ValidationRule::unique('permissions', 'guard_name'),
            ],
            'permissions_ids' => 'required',
            'department_id' => 'required',
            
        ], $messages);
    
    

    // Handle validation failure
    if ($validatedData->fails()) {
        return redirect()->back()->withErrors($validatedData)->withInput();
    }


        try {
            // dd("");
            $permission_ids = implode(",", $request->permissions_ids);
            // dd( $permission_ids);
            // Create the rule
            $rule = new Rule();
            $rule->name = $request->name;
            $rule->department_id = $request->department_id;
            $rule->permission_ids = $permission_ids;
            $rule->save();
            // Dynamically create model instance based on the model class string
            return view('role.view');
            // return response()->json("ok");
            // dd("sara");
            // return redirect()->back()->with('alert', 'Permission created successfully.')
            // return redirect()->back()->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            // dd("yy");
            return response()->json($e->getMessage());
            // return redirect()->back()->with('error', 'Failed to create permission. ' . );
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // dd("ss");
        $rule_permission = Rule::find($id);
        $allpermission = Permission::get();

        $permission_ids = explode(',', $rule_permission->permission_ids);

            // Fetch all permissions that the user has access to based on their role
        $hisPermissions = Permission::whereIn('id', $permission_ids)->get();
        $user = User::find(Auth::user()->id);
        if($user->flag == "user")
        {
            $alldepartment = departements::where('id',$user->department_id)->orwhere('parent_id',$user->department_id)->get();
        }
        else
        {
            $alldepartment = departements::where('id',$user->public_administration)->orwhere('parent_id',$user->public_administration)->get();
        }

        // $alldepartment =$user->createdDepartments;
       
        // dd($allPermissions);

        return view('role.show' ,compact('allpermission','alldepartment','hisPermissions','rule_permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
        // dd( $id);
        $rule_permission = Rule::find($id);
        $allpermission = Permission::get();

        $permission_ids = explode(',', $rule_permission->permission_ids);

            // Fetch all permissions that the user has access to based on their role
        $hisPermissions = Permission::whereIn('id', $permission_ids)->get();
        $user = User::find(Auth::user()->id);
        if($user->flag == "user")
        {
            $alldepartment = departements::where('id',$user->department_id)->orwhere('parent_id',$user->department_id)->get();
        }
        else
        {
            $alldepartment = departements::where('id',$user->public_administration)->orwhere('parent_id',$user->public_administration)->get();
        }
        // $alldepartment =$user->createdDepartments;
       
        // dd($allPermissions);

        return view('role.edit' ,compact('allpermission','alldepartment','hisPermissions','rule_permission'));


    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id ,Request $request)
    {
        // dd($request);

        // $request->validate([
        //     'name' => 'required|string',
        //     'permissions_ids' => 'required',
        //     // 'department_id' => 'required',
        // ]);
        $messages = [
            'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
            'permissions_ids.required' => 'الصلاحية   مطلوب ولا يمكن تركه فارغاً.',

            'department_id.required' => 'الادارة  مطلوب ولا يمكن تركه فارغاً.',
            // 'name.unique' => 'رقم الهاتف يجب أن يكون نصاً.',

            // Add more custom messages here
        ];
        
        $validatedData = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                // ValidationRule::unique('permissions', 'guard_name'),
            ],
            'permissions_ids' => 'required',
            'department_id' => 'required',
            
        ], $messages);
    
    

    // Handle validation failure
    if ($validatedData->fails()) {
        return redirect()->back()->withErrors($validatedData)->withInput();
    }
        try {
            // dd("");
            $permission_ids = implode(",", $request->permissions_ids);
            // dd( $permission_ids);
            // Create the rule
            $rule = Rule::find($id);
            $rule->name = $request->name;
            $rule->department_id = $request->department_id;
            $rule->permission_ids = $permission_ids;
            $rule->save();
            return view('role.view');
            // Dynamically create model instance based on the model class string
            // return response()->json("ok");
            // dd("sara");
            // return redirect()->back()->with('alert', 'Permission created successfully.')
            // return redirect()->back()->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            // dd("yy");
            return response()->json($e->getMessage());
            // return redirect()->back()->with('error', 'Failed to create permission. ' . );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rule $rule)
    {
        //
    }
}
