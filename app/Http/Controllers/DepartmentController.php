<?php

namespace App\Http\Controllers;
use App\Models\departements;
use App\Models\User;
use App\DataTables\DepartmentDataTable;

use App\Http\Requests\StoreDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DepartmentDataTable $dataTable)
    {
        return $dataTable->render('departments.index');
        // $departments = departements::with(['manager', 'managerAssistant'])->paginate(10);
        // return view('departments.index', compact('departments'));
        // return response()->json($departments);
    }


    public function index_1()
    {
        $departments = departements::with(['manager', 'managerAssistant'])->paginate(10);
        return view('sub_departments.index', compact('departments'));
        // return response()->json($departments);
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
