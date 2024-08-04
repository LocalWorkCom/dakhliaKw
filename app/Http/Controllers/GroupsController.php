<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\WorkingTime;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // if ($request->ajax()) {
        //     $data = Groups::all();
        //     return DataTables::of($data)
        //         ->addColumn('action', function($row){
        //             return '<button type="button" class="btn btn-primary edit-btn" data-id="'.$row->id.'">Edit</button>';
        //         })
        //         ->make(true);
        // }
        $workTimes = WorkingTime::all(); // Fetch all work times from the database
        return view('group.view' , compact('workTimes'));
    }

    public function getgroups()
    {
        $data = Groups::all();
        // dd($data);

        return DataTables::of($data)->addColumn('action', function ($row) {

            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
        ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'work_time_id' => 'required|string|max:255',
            'points_inspector' => 'required|integer',
        ]);
    
        Groups::create([
            'name' => $request->name,
            'work_time_id' => $request->work_time_id,
            'points_inspector' => $request->points_inspector,
        ]);

        return redirect()->route('groups.index')->with('message', 'Group created successfully');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("group.add");
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         // Add other validation rules as needed
    //     ]);

    //     Groups::create([
    //         'name' => $request->name,
    //         'work_time_id' => $request->work_time_id,
    //         'points_inspector' => $request->points_inspector,
    //                     // Add other fields as needed
    //     ]);

    //     return redirect()->route('group.view')->with('message', 'Group created successfully');
    // }

    /**
     * Display the specified resource.
     */
    public function show(Groups $group)
    {
        return view('group.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Groups $group)
    {
        return view('group.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Groups $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Add other validation rules as needed
        ]);

        $group->update([
            'name' => $request->name,
            'work_time_id' => $request->work_time_id,
            'points_inspector' => $request->points_inspector,

            // Add other fields as needed
        ]);

        return redirect()->route('group.view')->with('message', 'Group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groups $group)
    {
        $group->delete();

        return redirect()->route('group.view')->with('message', 'Group deleted successfully');
    }
}
