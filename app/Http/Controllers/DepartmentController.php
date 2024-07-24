<?php

namespace App\Http\Controllers;
use App\Models\departements;
use App\Models\User;
use App\DataTables\DepartmentDataTable;
use App\DataTables\subDepartmentsDataTable;

use App\Http\Requests\StoreDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

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
        ->withCount('children')->get();

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
        return view('sub_departments.index');
    }
    public function getSub_Department()
    {
        $data = departements::withCount('children')
        ->where('parent_id', Auth::user()->department_id)
        ->with(['children'])->get();

    return DataTables::of($data)
        ->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>';
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
         return view('departments.create', compact('users','departments'));
    }


    public function create_1()
    {
        // dd(Auth::user());
        $users = User::all();
        $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();

        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();         
        return view('sub_departments.create', compact('parentDepartment','departments'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        
        $request->validate([
            'name' => 'required',
            'manger' => 'required',
            'manger_assistance' => 'required',
        ]);
         $departements =departements::create($request->all());
          $departements->created_by = Auth::user()->id;

          $departements->save();
        //   dd($departements);
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
        // return response()->json($department, 201);
    }


    public function store_1(Request $request)
    {
        // dd($request->all());
        
        $request->validate([
        ]);
         $departements =departements::create($request->all());
          $departements->created_by = Auth::user()->id;

          $departements->save();
        //   dd($departements);
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
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

        // Get the children of the parent department
        $departments = $parentDepartment ? $parentDepartment->children : collect();   
        return view('sub_departments.edit', compact('department', 'departments' ,'parentDepartment'));
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
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
        // return response()->json($department);
    }

    public function update_1(Request $request, departements $department)
    {
        $request->validate([
            
        ]);

        $department->update($request->all());
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
