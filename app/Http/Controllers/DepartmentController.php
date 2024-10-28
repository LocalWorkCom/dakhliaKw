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


    public function index_1()
    {
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

        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $users = User::where(function ($query) {
                $query->where('rule_id', '!=', 2)
                    ->orWhereNull('rule_id');
            })->where('id', '<>', Auth::user()->id)
                ->where('flag', 'user')->get();
            $departments = departements::with('children', 'parent')->get();

            $employee = User::where('flag', 'employee')
                ->where(function ($query) {
                    $query->whereNull('rule_id')
                        ->orWhereNotIn('rule_id', [1, 2]);
                })
                ->get();
        } else {
            $userDepartment = auth()->user()->department_id;
            $childDepartmentIds = [];
            if ($userDepartment) {
                $childDepartments = departements::where('parent_id', $userDepartment)->pluck('id')->toArray();
                $childDepartmentIds = array_merge([$userDepartment], $childDepartments);
            }

            $users = User::where(function ($query) {
                $query->where('rule_id', '!=', 2)
                    ->orWhereNull('rule_id');
            })
                ->where('id', '<>', auth()->user()->id)
                ->where('flag', 'user')
                ->where(function ($query) use ($childDepartmentIds) {
                    $query->whereNull('department_id')
                        ->orWhereIn('department_id', $childDepartmentIds);
                })
                ->get();

            $departments = departements::with('children', 'parent')->get();

            $employee = User::where('flag', 'employee')
                ->where(function ($query) {
                    $query->whereNull('rule_id')
                        ->orWhereNotIn('rule_id', [1, 2]);
                })
                ->where(function ($query) use ($childDepartmentIds) {
                    $query->whereNull('department_id')
                        ->orWhereIn('department_id', $childDepartmentIds);
                })
                ->get();
        }

        return view('departments.create', compact('users', 'departments', 'employee'));
    }


    public function create_1()
    {
        // dd(Auth::user());
        $users = User::where('rule_id', '<>', 2)->where('department_id', NULL)->where('id',)->get();
        $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();

        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();
        $subdepartments = departements::with('children')->get();
        return view('sub_departments.create', compact('parentDepartment', 'departments', 'subdepartments', 'users'));
    }

    public function getEmployeesByDepartment($departmentId)
    {
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
        ], [
            'name.required' => 'الرجاء إدخال اسم القطاع.',
            'manger.required' => 'الرجاء اختيار المدير.',
        ]);


        $departements = new Departements();
        $departements->name = $request->name;
        $departements->manger = $request->manger;
        $departements->description = $request->description;

        $departements->parent_id = Auth::user()->department_id;
        $departements->created_by = Auth::user()->id;
        $departements->save();

        $user = User::find($request->manger);
        $user->department_id = $departements->id;
        $user->save();

        if ($request->has('employess')) {
            foreach ($request->employess as $item) {
                $user = User::find($item);
                if ($user) {
                    $user->department_id = $departements->id;
                    $user->save();
                }
            }
        }
        return redirect()->route('departments.index')->with('success', 'تم أضافه الأداره بنجاح . ');
    }


    public function store_1(Request $request)
    {
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
                $user = User::find($item);
                if ($user) {
                    $user->department_id = $departements->id;
                    $user->save();
                }
            }
        }
        return redirect()->route('sub_departments.index')->with('success', 'Department created successfully.');
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
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $users = User::where(function ($query) {
                $query->where('rule_id', '!=', 2)
                    ->orWhereNull('rule_id');
            })->where('id', '<>', Auth::user()->id)
                ->where('flag', 'user')->get();
            $departments = departements::with('children', 'parent')->get();
            $employee = User::where('flag', 'employee')
                ->where(function ($query) {
                    $query->whereNull('rule_id')
                        ->orWhereNotIn('rule_id', [1, 2]);
                })
                ->get();
        } else {
            $userDepartment = auth()->user()->department_id;
            $childDepartmentIds = [];
            if ($userDepartment) {
                $childDepartments = departements::where('parent_id', $userDepartment)->pluck('id')->toArray();
                $childDepartmentIds = array_merge([$userDepartment], $childDepartments);
            }

            $users = User::where(function ($query) {
                $query->where('rule_id', '!=', 2)
                    ->orWhereNull('rule_id');
            })
                ->where('id', '<>', auth()->user()->id)
                ->where('flag', 'user')
                ->where(function ($query) use ($childDepartmentIds) {
                    $query->whereNull('department_id')
                        ->orWhereIn('department_id', $childDepartmentIds);
                })
                ->get();

            $departments = departements::with('children', 'parent')->get();

            $employee = User::where('flag', 'employee')
                ->where(function ($query) {
                    $query->whereNull('rule_id')
                        ->orWhereNotIn('rule_id', [1, 2]);
                })
                ->where(function ($query) use ($childDepartmentIds) {
                    $query->whereNull('department_id')
                        ->orWhereIn('department_id', $childDepartmentIds);
                })
                ->get();
        }
        // dd($users,$employee);
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
        $request->validate([
            'name' => 'required',
            'manger' => 'required',
        ], [
            'name.required' => 'الرجاء إدخال اسم القطاع.',
            'manger.required' => 'الرجاء اختيار المدير.',
        ]);

        $currentManagerId = $department->manger;
        $newmanagerrequest = intval($request->manger);
        if ($currentManagerId != $newmanagerrequest) {
            if ($currentManagerId) {
                $oldManager = User::find($currentManagerId);
                if ($oldManager) {
                    $oldManager->department_id = null;
                    $oldManager->save();
                }
            }

            $newManager = User::find($newmanagerrequest);
            if ($newManager) {
                $newManager->department_id = $department->id;
                $newManager->save();
            }
        }

        $department->update($request->only(['name', 'manger', 'description']));

        $currentEmployeeIds = $department->employees()->where('flag', 'employee')->pluck('id')->toArray();

        $requestedEmployeeIds = $request->input('employees', []);

        $employeesToRemove = array_diff($currentEmployeeIds, $requestedEmployeeIds);

        foreach ($employeesToRemove as $item) {
            $user = User::find($item);
            if ($user) {
                $user->department_id = null;
                $user->save();
            }
        }

        foreach ($requestedEmployeeIds as $item) {
            $user = User::find($item);
            if ($user) {
                $user->department_id = $department->id;
                $user->save();
            }
        }

        return redirect()->route('departments.index')->with('success', 'تم تعديل الاداره بنجاح');
    }


    public function update_1(Request $request, departements $department)
    {
        $request->validate([]);

        $department->update($request->all());

        if ($request->has('employess')) {
            foreach ($request->employess as $item) {
                $user = User::find($item);

                if ($user) {
                    $user->department_id = $department->id;
                    $user->save();
                }
            }
        }
        return redirect()->route('sub_departments.index')->with('success', 'Department updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(departements $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
