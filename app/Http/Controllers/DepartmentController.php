<?php

namespace App\Http\Controllers;
use App\Models\departements;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = departements::all();
        // return view('departments.index', compact('departments'));
        return response()->json($departments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'manager_director' => 'required',
            'ass_manager_director' => 'required',
        ]);

        $department = departements::create($request->all());
        // return redirect()->route('departments.index')->with('success', 'Department created successfully.');
        return response()->json($department, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(departements $department)
    {
        // return view('departments.show', compact('department'));
        return response()->json($department);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, departements $department)
    {
        $request->validate([
            'name' => 'required',
            'manager_director' => 'required',
            'ass_manager_director' => 'required',
        ]);

        $department->update($request->all());
        // return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
        return response()->json($department);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(departements $department)
    {
        $department->delete();
        // return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
        return response()->json(null, 204);
    }
}
