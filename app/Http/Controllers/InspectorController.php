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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

    public function update(Request $request, Inspector $inspector)
    {
        $request->validate([
            
        ]);

        $inspector->update($request->only(['position', 'name', 'phone', 'type']));

        return redirect()->route('inspectors.index')
                         ->with('success', 'Inspector updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
