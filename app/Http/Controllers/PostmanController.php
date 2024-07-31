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
        $rules = [
            'name' => ['required'],
            'national_id' => ['required', 'unique:postmans,national_id'],
            'department_id' => ['required'],
            'phone1' => ['required','unique:postmans,phone1'],
            'phone2' => 'required|unique:postmans,phone2',
        ];

        $messages = [
            'national_id.required' => 'يجب ادخال رقم الهوية',
            'national_id.unique' => 'عفوا رقم الهوية موجود من قبل',
            'department_id.required' => 'يجب ادخال الادارة',
            'phone1.required' => 'يجب ادخال رقم الهاتف الاول',
            'phone1.unique' => 'عفوا رقم الهاتف الاول موجود من قبل',
            'phone2.required' => 'يجب ادخال رقم الهاتف الثانى',
            'phone2.unique' => 'هذا الرقم موجود من قبل',
            'name.required' => 'يجب ادخال اسم الشخص',
        ];

        $validatedData = $request->validate($rules, $messages);

        // $request->validate([
        //     'name' => 'nullable|string|max:255',
        //     'national_id' => 'nullable|string|max:255|unique:postmans,national_id',
        //     'department_id' => 'nullable|string|max:255',
        //     'phone1' => 'nullable|string|max:255|unique:postmans,phone1',
        //     'phone2' => 'nullable|string|max:255|unique:postmans,phone2',
        // ]);

        // Create the Postman record
        $postman = Postman::create($validatedData);
        $postman->created_by = Auth::user()->id;
        $postman->save();
        
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
