<?php

namespace App\Http\Controllers;
use App\Models\departements;
use App\Models\User;
use App\DataTables\DepartmentDataTable;

use App\Http\Requests\StoreDepartmentRequest;
use Illuminate\Http\Request;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
         return view('departments.create', compact('users'));
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
         departements::create($request->all());
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
        // return response()->json($department, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $department = departements::with(['manager', 'managerAssistant'])->findOrFail($id);
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
