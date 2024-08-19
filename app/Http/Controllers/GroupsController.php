<?php

namespace App\Http\Controllers;

use App\Models\Government;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\WorkingTime;

use App\Models\WorkingTree;

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
        // $workTimes = WorkingTime::all();
        // $inspector = Inspector::where('group_id',$id)->get();
        // dd($workTimes);
        $governments = Government::all();
        return view('group.view', compact('governments'));
    }

    public function getgroups()
    {
        $data = Groups::with('government')->get();
        // $data = Groups::all();
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
            ->addColumn('num_inspectors', function ($row) {
                $count = Inspector::where('group_id', $row->id)->count();

                if ($count == 0) {
                    $btn = '<a class="btn btn-sm"  style="background-color: #F7AF15; padding-inline: 15px;" href=' . route('group.groupcreateInspectors', $row->id) . '> ' . $count . '</a>';
                } else {
                    $btn = '<a class="btn btn-sm"  style="    background-color: #274373; padding-inline: 15px" href=' . route('group.groupcreateInspectors', $row->id) . '> ' . $count . '</a>';
                }
                return  $btn;
            })
            ->addColumn('num_team', function ($row) {
                $count = GroupTeam::where('group_id', $row->id)->count();
                if ($count == 0) {
                    $btn = '<a class="btn btn-sm" style="background-color: #F7AF15; padding-inline: 15px;"href=' . route('groupTeam.index', $row->id) . '> ' . $count . '</a>';
                } else {

                    $btn = '<a class="btn btn-sm"  style="background-color: #274373; padding-inline: 15px" href=' . route('groupTeam.index', $row->id) . '> ' . $count . '</a>';
                }
                return  $btn;
            })
            ->rawColumns(['action', 'num_inspectors', 'num_team'])
            ->make(true);
    }


    public function groupCreateInspectors($id)
    {
        $inspectors = Inspector::whereNull('group_id')->get();
        $inspectorsIngroup = Inspector::where('group_id', $id)->get();
        return view('group.inspector', compact('inspectors', 'inspectorsIngroup', 'id'));
    }
    public function groupAddInspectors(Request $request, $id)
    {
        if (isset($request->inspectorein)) {

            $allExist  = Inspector::where('group_id', $id)->pluck('id');
            foreach ($allExist as $row_id) {
                if (!in_array($row_id, $request->inspectorein)) {
                    $inspector = Inspector::findOrFail($row_id);
                    $inspector->group_id = null;
                    $inspector->save();
                }
            }
        }
        if (isset($request->inspectorein)) {

            foreach ($request->inspectorein as $row_id) {

                $inspector = Inspector::findOrFail($row_id);
                $inspector->group_id = $id;
                $inspector->save();
            }
        } else {

            $inspectorsCheck = Inspector::where('group_id', $id)->get();
            if ($inspectorsCheck->count()) {

                foreach ($inspectorsCheck as $inspector) {
                    $inspector->group_id = null;
                    $inspector->save();
                }
            }
        }
        if (isset($request->inspectore)) {

            foreach ($request->inspectore as $row_id) {
                $inspector = Inspector::findOrFail($row_id);
                $inspector->group_id = $id;
                $inspector->save();
            }
        }
        return redirect()->route('group.view')->with('success', 'تم اضافه مفتشين بنجاح.');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'points_inspector.required' => 'نقاط التفتيش مطلوبة ولا يمكن تركها فارغة.',
            'government_id.required' => 'المحافظة مطلوبة ولا يمكن تركه فارغا'

        ];

        $validatedData = Validator::make($request->all(), [
            'name' => 'required',
            'points_inspector' => 'required',
            'government_id' => 'required',

        ], $messages);

        // dd($validatedData);
        // Handle validation failure
        if ($validatedData->fails()) {
            // session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withErrors($validatedData)->withInput()->with('showModal', true);

            // return redirect()->back();
        }

        try {
            $group = new Groups();
            $group->name = $request->name;
            $group->points_inspector = $request->points_inspector;
            $group->government_id = $request->government_id;

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
        $working_time = WorkingTree::find($group->work_time_id);

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
        $working_time = WorkingTree::find($group->work_time_id);


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
            'points_inspector_edit.required' => 'نقاط التفتيش مطلوبة ولا يمكن تركها فارغة.',
            'government_id.required' => 'المحافظة مطلوبة ولا يمكن تركه فارغا'
        ];

        $validatedData = Validator::make($request->all(), [
            'name_edit' => 'required',
            'points_inspector_edit' => 'required',
            'government_id' => 'required|integer',
        ], $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput()->with('editModal', true);
        }

        $group = Groups::find($request->id_edit);

        $updated = false;

        // Check each field individually to see if it has changed
        if ($group->name !== $request->name_edit) {
            $group->name = $request->name_edit;
            $updated = true;
        }

        if ($group->points_inspector !== $request->points_inspector_edit) {
            $group->points_inspector = $request->points_inspector_edit;
            $updated = true;
        }

        if ($group->government_id !== $request->government_id) {
            $group->government_id = $request->government_id;
            $updated = true;
        }

        // If nothing was updated, return with an error and show the modal again
        if (!$updated) {
            return redirect()->back()->withErrors(['nothing_updated' => 'لم يتم تحديث أي بيانات.'])->withInput()->with('editModal', true);
        }

        $group->save();

        session()->flash('success', 'تم تعديل مجموعة بنجاح.');

        return redirect()->back();
    }

    // public function update(Request $request)
    // {
    //     $messages = [
    //         'name_edit.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
    //         'points_inspector_edit.required' => 'نقاط التفتيش مطلوبة ولا يمكن تركها فارغة.',
    //         'government_id.required' => 'المحافظة مطلوبة ولا يمكن تركه فارغا'

    //     ];

    //     $validatedData = Validator::make($request->all(), [
    //         'name_edit' => 'required',
    //         'points_inspector_edit' => 'required',
    //         'government_id' => 'required|integer',

    //     ], $messages);

    //     // // Handle validation failure
    //     // if ($validatedData->fails()) {
    //     //     return redirect()->back()->withErrors($validatedData)->withInput()->with('editeModal', true);
    //     // }
    //     if ($validatedData->fails()) {
    //         // session()->flash('errors', $validatedData->errors());
    //         return redirect()->back()->withErrors($validatedData)->withInput()->with('editModal', true);

    //         // return redirect()->back();
    //     }
        
    //     $group = Groups::find($request->id_edit);

        
    //     $group->name = $request->name_edit;
    //     $group->points_inspector = $request->points_inspector_edit;
    //     $group->government_id = $request->government_id;


    //     // if ($group->name === $request->name_edit && $group->points_inspector == $request->points_inspector_edit && $group->government_id === $request->government_id) {
    //     //     return redirect()->back()->withErrors(['nothing_updated' => 'لم يتم تحديث أي بيانات.'])->withInput()->with('editModal', true);

    //     // }

    //     $group->save();
    //     session()->flash('success', 'تم تعديل مجموعة بنجاح.');

    //     return redirect()->back();
    // }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groups $group)
    {
        $group->delete();

        return redirect()->route('group.view')->with('message', 'Group deleted successfully');
    }
}
