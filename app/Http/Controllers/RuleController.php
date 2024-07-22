<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\User;
use App\Models\Permission;
use App\Models\departements;
use Illuminate\Http\Request;
use App\DataTables\RoleDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRuleRequest;
use App\Http\Requests\UpdateRuleRequest;

class RuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RoleDataTable $dataTable)
    {
        //

        return $dataTable->render('role.view');
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
        $alldepartment =$user->createdDepartments;
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
        $request->validate([
            'name' => 'required|string',
            'permissions_ids' => 'required',
            'department_id' => 'required',
        ]);


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

            return response()->json("ok");
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
        $alldepartment =$user->createdDepartments;
       
        // dd($allPermissions);

        return view('role.edit' ,compact('allpermission','alldepartment','hisPermissions','rule_permission'));


    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id ,Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'permissions_ids' => 'required',
            // 'department_id' => 'required',
        ]);
        try {
            // dd("");
            $permission_ids = implode(",", $request->permissions_ids);
            // dd( $permission_ids);
            // Create the rule
            $rule = Rule::find($id);
            $rule->name = $request->name;
            // $rule->department_id = $request->department_id;
            $rule->permission_ids = $permission_ids;
            $rule->save();
            // Dynamically create model instance based on the model class string
            return response()->json("ok");
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
