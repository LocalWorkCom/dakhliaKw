<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\departements;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\DataTables\DepartmentDataTable;
use Illuminate\Support\Facades\Validator;
use App\DataTables\subDepartmentsDataTable;
use App\Http\Requests\StoreDepartmentRequest;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(DepartmentDataTable $dataTable)
    // {
    //     return $dataTable->render('departments.index');
    //     // $departments = departements::with(['manager', 'managerAssistant'])->paginate(10);
    //     // return view('departments.index', compact('departments'));
    //     // return response()->json($departments);
    // }
    public function index()
    {
        //
        // return $dataTable->render('permission.view');
        return view('departments.index');
    }
    public function getDepartment()
    {
        $data = departements::withCount('iotelegrams')
        ->withCount('outgoings')
        ->withCount('children')->orderBy('id', 'desc')->get();

    return DataTables::of($data)
        ->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
        ->addColumn('iotelegrams_count', function ($row) {
            return $row->iotelegrams_count;  // Display the count of iotelegrams
        })
        ->addColumn('outgoings_count', function ($row) {
            return $row->outgoings_count;
        })
        ->addColumn('children_count', function ($row) { // New column for departments count
            return $row->children_count;
        })
        ->addColumn('manager_name', function ($row) {
            return $row->manager ? $row->manager->name : 'N/A'; // Display the manager's name
        })
        ->rawColumns(['action'])
        ->make(true);
    }



    // public function index_1(subDepartmentsDataTable $dataTable)
    // {
    //     return $dataTable->render('sub_departments.index');
    //     // $departments = departements::with(['manager', 'managerAssistant'])->paginate(10);
    //     // return view('sub_departments.index', compact('departments'));
    //     // return response()->json($departments);
    // }

    public function index_1()
    {
        //
        // return $dataTable->render('permission.view');
        $users = User::where('flag', 'employee')->where('department_id', NULL)->get();
        $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();

        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();    
        if(Auth::user()->rule_id == 2)
        {
            $subdepartments = departements::with('children')->get();
        }
        else
        {
            $subdepartments = departements::where('id',Auth::user()->department_id)->with('children')->get();
        }
        
        return view('sub_departments.index', compact('users','subdepartments','departments','parentDepartment'));
    }
    public function getSub_Department()
    {
        $data = departements::withCount('children')
        ->where('parent_id', Auth::user()->department_id)
        ->with(['children'])->orderBy('created_at', 'asc')->get();

        // $data = departements::all();

    return DataTables::of($data)
        ->addColumn('action', function ($row) {
            return '<button class="btn  btn-sm" style="background-color: #259240;"><i class="fa fa-edit"></i></button>';
        })

        ->addColumn('children_count', function ($row) { // New column for departments count
            return $row->children_count;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // dd(Auth::user());
        $users = User::all();
        $departments = departements::with('children', 'parent')->get();
        $employee = User::where('flag', 'employee')->where('department_id', NULL)->get();
         return view('departments.create', compact('users','departments','employee'));
    }


    public function create_1()
    {
        // dd(Auth::user());
        $users = User::where('flag', 'employee')->where('department_id', NULL)->get();
        $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();

        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();       
        $subdepartments = departements::with('children')->get();
        return view('sub_departments.create', compact('parentDepartment','departments','subdepartments','users'));
    }

    public function getEmployeesByDepartment($departmentId)
    {
        // $currentEmployees = $department->employees()->pluck('id')->toArray();

        try {
            $employees = User::where('department_id', $departmentId)->get();
            return response()->json($employees);
        } catch (\Exception $e) {
            \Log::error('Error fetching employees: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching employees'], 500);
        }
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);

        $request->validate([
            'name' => 'required',
            'manger' => 'required',
        ]);
         $departements =departements::create($request->all());
          $departements->created_by = Auth::user()->id;

          $departements->save();

            $user = User::find($request->manger);
            $user->department_id = $departements->id;
            $user->save();

          if($request->has('employess'))
          {
            foreach($request->employess as $item)
            {
                // dd($item);
                $user = User::find($item);

                $log = DB::table('user_departments')->insert([
                    'user_id' => $user->id,
                    'department_id' => $departements->id,
                    'flag' => "1",
                    'created_at' => now(),
                ]);
                $user = User::find($item);
                $user->department_id = $departements->id;
                $user->save();
            }
          }
        //   dd($departements);
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
        // return response()->json($department, 201);
    }


    public function store_1(Request $request)
    {
        // dd($request);

        $rules = [
            'name' => 'required',
            'manger' => 'required',
            'parent_id' => 'required',

        ];

        $messages = [
            'name.required' => 'يجب ادخال اسم الادارة',

            'manger.required' => 'يجب ادخال المدير',
        
            'parent_id.required' => 'يجب ادخال القطاع',
           
        ];
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
         $departements =departements::create($request->all());
          $departements->created_by = Auth::user()->id;

          $departements->save();

            $user = User::find($request->manger);
            $user->department_id = $departements->id;
            $user->save();

          if($request->has('employess'))
          {
            foreach($request->employess as $item)
            {
                // dd($item);

                $user = User::find($item);

                $log = DB::table('user_departments')->insert([
                    'user_id' => $user->id,
                    'department_id' => $departements->id,
                    'flag' => "1",
                    'created_at' => now(),
                ]);

                $user = User::find($item);
                $user->department_id = $departements->id;
                $user->save();
            }
          }
        //   dd($departements);
        return redirect()->route('sub_departments.index')->with('success', 'Department created successfully.');
        // return response()->json($department, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $department = departements::with(['manager', 'managerAssistant','children', 'parent'])->findOrFail($id);
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(departements $department)
    {
        $users = User::all();
        return view('departments.edit', compact('department', 'users'));
    }

    public function edit_1(departements $department)
    {
        $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();
        $users = User::where('flag', 'employee')->where('department_id', NULL)->get();
        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();  
        $subdepartments = departements::with('children', 'parent')->get();
        return view('sub_departments.edit', compact('department', 'departments' ,'parentDepartment','subdepartments','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, departements $department)
    {
        $request->validate([
            'name' => 'required',
            'manger' => 'required',
            'manger_assistance' => 'required',
        ]);

        $department->update($request->all());
        return redirect()->route('sub_departments.index')->with('success', 'Department updated successfully.');
        // return response()->json($department);
    }

    public function update_1(Request $request, departements $department)
    {
        $request->validate([

        ]);

        $department->update($request->all());

        if($request->has('employess'))
          {
            foreach($request->employess as $item)
            {
                // dd($item);

                $user = User::find($item);

                $log = DB::table('user_departments')->updateOrInsert([
                    'user_id' => $user->id,
                    'department_id' => $departements->id,
                    'flag' => "1",
                    'created_at' => now(),
                ]);

                $user = User::find($item);
                $user->department_id = $departements->id;
                $user->save();
            }
          }
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
        // return response()->json($department);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(departements $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
        // return response()->json(null, 204);
    }
}
