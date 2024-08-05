<?php

namespace App\Http\Controllers;
use App\Models\Inspector;
use App\Models\User;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $inspectors = Inspector::all();
        return view('inspectors.index');
    }
    public function addToGroup(Request $request)
    {
       $inspuctor = Inspector::findOrFail($request->id);
       $inspuctor->group_id = $request->group_id;
       $inspuctor->save();
       return redirect()->route('inspectors.index')->with('success', 'Inspector created successfully.')->with('showModal', true);
    }

    public function getInspectors()
    {
        $data = Inspector::orderBy('id', 'desc')->get();

        return DataTables::of($data)
        ->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
        
        ->rawColumns(['action'])
        ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::get();
         return view('inspectors.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            
        ]);
         $inspector =Inspector::create($request->all());

          $inspector->save();

        //   dd($departements);
        return redirect()->route('inspectors.index')->with('success', 'Inspector created successfully.')->with('showModal', true);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $inspector = Inspector::findOrFail($id);
        $users = User::get();
        return view('inspectors.show', compact('inspector','users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $inspector = Inspector::find($id);
        // dd($inspector);
        $users = User::get();
        return view('inspectors.edit', compact('inspector','users'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        
        $request->validate([
            
        ]);
        $inspector = Inspector::find($id);
        $inspector->update($request->only(['position', 'name', 'phone', 'type']));
        // $inspector->save();
        // dd($inspector->id);
        return redirect()->back()
                         ->with('success', 'Inspector updated successfully.')->with('showModal', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
