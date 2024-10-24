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
        $users = User::where('flag', 'employee')->where('department_id', NULL)->get();

        $parentDepartment = $departments = departements::where('parent_id', Auth::user()->department_id)->first();
        // Get the children of the parent department

        // $departments = $parentDepartment ? $parentDepartment->children : collect();
        // if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
        //     $subdepartments = departements::with('children')->get();
        // } else {
        //     $subdepartments = departements::where('id', Auth::user()->department_id)->with('children')->get();
        // }

        return view('departments.index', compact('users', 'departments', 'parentDepartment'));
    }
    public function getDepartment()
    {
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $data = departements::withCount('iotelegrams')
                ->withCount('outgoings')
                ->withCount('children')
                ->with(['children'])
                ->orderBy('id', 'desc')->get();
        } else {
            $data = departements::withCount('iotelegrams')
                ->withCount('children')

                ->withCount('outgoings')->where(function ($query) {
                    $query->where('id', Auth::user()->department_id)
                        ->orWhere('parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                })
                ->with(['children'])
                ->orderBy('id', 'desc')->get();
        }


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
        if (Auth::user()->rule_id == 2) {
            $subdepartments = departements::with('children')->get();
        } else {
            $subdepartments = departements::where('id', Auth::user()->department_id)->with('children')->get();
        }

        return view('sub_departments.index', compact('users', 'subdepartments', 'departments', 'parentDepartment'));
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

        $users = User::where(function ($query) {
            $query->where('rule_id', '!=', 2)
                ->orWhereNull('rule_id'); // Include rows where 'rule_id' is null
        })
            ->where('id', '<>', Auth::user()->id)
            ->where('flag', 'user')
            ->where(function ($query) {
                $query->whereNull('department_id')
                    ->orWhere('department_id', auth()->user()->department_id); // Include rows where 'rule_id' is null
            })
            ->get();

        $departments = departements::with('children', 'parent')->get();

        $employee = User::where('flag', 'employee')
        ->where(function ($query) {
            $query->whereNull('rule_id') // Include rows where 'rule_id' is null
                  ->orWhereNotIn('rule_id', [1, 2]); // Exclude rule_id 1 and 2 for non-null values
        })
        ->where(function ($query) {
            $query->whereNull('department_id') // Include rows where department_id is null
                  ->orWhere('department_id', auth()->user()->department_id); // Or matches the user's department_id
        })
        ->get();

        return view('departments.create', compact('users', 'departments', 'employee'));
    }


    public function create_1()
    {
        // dd(Auth::user());
        $users = User::where('rule_id', '<>', 2)->where('department_id', NULL)->get();
        $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();

        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();
        $subdepartments = departements::with('children')->get();
        return view('sub_departments.create', compact('parentDepartment', 'departments', 'subdepartments', 'users'));
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

        $request->validate([
            'name' => 'required',
            'manger' => 'required',
        ]);


        $departements = new Departements();
        $departements->name = $request->name;
        $departements->manger = $request->manger;
        $departements->parent_id = Auth::user()->department_id;
        $departements->created_by = Auth::user()->id;
        $departements->save();

        $user = User::find($request->manger);
        $user->department_id = $departements->id;
        $user->save();

        if ($request->has('employess')) {
            foreach ($request->employess as $item) {
                // dd($item);
                $user = User::find($item);

                if ($user) {
                    $user->department_id = $departements->id;
                    $user->save();
                    // dd($user);
                }
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
        $departements = departements::create($request->all());
        $departements->created_by = Auth::user()->id;

        $departements->save();

        $user = User::find($request->manger);
        $user->department_id = $departements->id;
        $user->save();

        if ($request->has('employess')) {
            foreach ($request->employess as $item) {
                // dd($item);

                $user = User::find($item);
                if ($user) {
                    $user->department_id = $departements->id;
                    $user->save();
                    // dd($user);
                }
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
        $department = departements::with(['manager', 'managerAssistant', 'children', 'parent'])->findOrFail($id);
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departements $department)
    {
        // Get all users (if you need them for a different part of the form)
        $users = User::all();

        // Query employees with filtering on rule_id and department_id
        $employee = User::where('flag', 'employee')
            ->where(function ($query) {
                $query->whereNull('rule_id')  // Include rows where 'rule_id' is null
                      ->orWhereNotIn('rule_id', [1, 2]);  // Exclude rule_id 1 and 2 for non-null values
            })
            ->where(function ($query) use ($department) {
                $query->whereNull('department_id')  // Include rows where department_id is null
                      ->orWhere('department_id', $department->id);  // Or matches the department being edited
            })
            ->get();

        return view('departments.edit', compact('department', 'users', 'employee'));
    }


    public function edit_1(departements $department)
    {
        $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();
        $users = User::where('flag', 'employee')->where('department_id', NULL)->get();
        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();
        $subdepartments = departements::with('children', 'parent')->get();
        return view('sub_departments.edit', compact('department', 'departments', 'parentDepartment', 'subdepartments', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, departements $department)
    {
        //dd($request);
        $request->validate([
            'name' => 'required',
            'manger' => 'required',
        ]);

        // Update the department details
        $department->update($request->only(['name', 'manger', 'description']));

        // Check if employees data is provided
        if ($request->has('employess')) {
            foreach ($request->employess as $item) {
                $user = User::find($item);

                if ($user) {
                    $user->department_id = $department->id;
                    $user->save();
                    // dd($user);
                }
            }
        }

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
        // return response()->json($department);
    }

    public function update_1(Request $request, departements $department)
    {
        $request->validate([]);

        $department->update($request->all());

        if ($request->has('employess')) {
            foreach ($request->employess as $item) {
                // dd($item);

                $user = User::find($item);

                if ($user) {
                    $user->department_id = $department->id;
                    $user->save();
                    // dd($user);
                }
            }
        }
        return redirect()->route('sub_departments.index')->with('success', 'Department updated successfully.');
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
