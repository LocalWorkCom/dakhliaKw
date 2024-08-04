<?php

namespace App\Http\Controllers;

use App\Models\Group;
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
        return view('group.view', compact('workTimes'));
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
    public function show($group)
    {
        // dd($group);
        $group = Group::find($group);
        $working_time = WorkingTime::find($group->work_time_id);

        $data =
            [
                'group' => $group,
                'working_time' => $working_time,
            ];
        // dd($group);
        if ($group) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
        // return view('group.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($group)
    {
        $group = Group::find($group);
        $working_time = WorkingTime::find($group->work_time_id);


        $data =
            [
                'group' => $group,
                'working_time' => $working_time,
            ];
        // dd($group);
        if ($group) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name_edit' => 'required|string|max:255',
            // Add other validation rules as needed
        ]);

        $group = Group::find($request->id_edit);
        $group->name = $request->name_edit;
        $group->points_inspector = $request->points_inspector_edit;
        $group->work_time_id = $request->work_time_id_edit;
        $group->save();
           

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
