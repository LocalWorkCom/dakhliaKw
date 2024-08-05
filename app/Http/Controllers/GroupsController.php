<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Groups;
use App\Models\WorkingTime;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Group as AttributesGroup;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // YourController.php

    public function index()
    {
        $workTimes = WorkingTime::all();
        // dd($workTimes);
        return view('group.view', compact('workTimes'));
    }

    public function getgroups()
    {
        $data = Groups::with('working_time')->get();
        // $data = Groups::all();
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
        $messages = [
            'name.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'work_time_id.required' => 'فترة العمل مطلوبة ولا يمكن تركها فارغة.',
            'points_inspector.required' => 'نقاط التفتيش مطلوبة ولا يمكن تركها فارغة.',
        ];

        $validatedData = Validator::make($request->all(), [
            'name' => 'required',
            'work_time_id' => 'required',
            'points_inspector' => 'required',
        ], $messages);

        // Handle validation failure
        if ($validatedData->fails()) {
            // session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withErrors($validatedData)->withInput()->with('showModal', true);

            // return redirect()->back();
        }

        try {
            $group = new Groups();
            $group->name = $request->name;
            $group->work_time_id = $request->work_time_id;
            $group->points_inspector = $request->points_inspector;
            $group->save();
            session()->flash('success', 'تم اضافه مجموعة بنجاح.');

            return redirect()->route('group.view');
        } catch (\Exception $e) {
            session()->flash('error',  'An error occurred while creating the group. Please try again');

            return redirect()->back();
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view("group.add");
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
        $group = Groups::find($group);
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
        $group = Groups::find($group);
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
        $messages = [
            'name_edit.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'work_time_id_edit.required' => 'فترة العمل مطلوبة ولا يمكن تركها فارغة.',
            'points_inspector_edit.required' => 'نقاط التفتيش مطلوبة ولا يمكن تركها فارغة.',
        ];

        $validatedData = Validator::make($request->all(), [
            'name_edit' => 'required',
            'work_time_id_edit' => 'required',
            'points_inspector_edit' => 'required',
        ], $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput()->with('editModal', true);
        }

        $group = Groups::find($request->id_edit);
        $hasChanges = false;

        if ($group->name !== $request->name_edit) {
            $group->name = $request->name_edit;
            $hasChanges = true;
        }
        if ($group->work_time_id !== $request->work_time_id_edit) {
            $group->work_time_id = $request->work_time_id_edit;
            $hasChanges = true;
        }
        if ($group->points_inspector !== $request->points_inspector_edit) {
            $group->points_inspector = $request->points_inspector_edit;
            $hasChanges = true;
        }

        if ($hasChanges) {
            $group->save();
            session()->flash('success', 'تم تعديل مجموعة بنجاح.');
        } else {
            session()->flash('message', 'لم يتم التعديل.');
        }

        return redirect()->back()->with('editModal', true);
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
