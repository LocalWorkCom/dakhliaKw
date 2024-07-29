<?php

namespace App\Http\Controllers;
use App\Models\Postman;
use App\Models\departements;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class PostmanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = departements::all();
        return view('postmans.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:255|unique:postmans,national_id',
            'department_id' => 'nullable|string|max:255',
            'phone1' => 'nullable|string|max:255|unique:postmans,phone1',
            'phone2' => 'nullable|string|max:255|unique:postmans,phone2',
        ]);

        $Postman = Postman::create($request->all());
        $Postman->created_by = Auth::user()->id;
        return redirect()->route('departments.index')->with('success', 'Postman created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Postman $postman)
    {
        return view('postmans.edit', compact('postman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
