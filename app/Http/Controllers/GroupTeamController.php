<?php

namespace App\Http\Controllers;

use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\WorkingTime;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreGroupTeamRequest;
use App\Http\Requests\UpdateGroupTeamRequest;
use App\Models\WorkingTree;

class GroupTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $workTrees = WorkingTree::all();

        //
        return view('groupteam.show', compact('id', 'workTrees'));
    }
    public function getGroupTeam($id)
    {
        $data = GroupTeam::with('group')->with('working_tree')->where('group_id', $id)->get();
        // dd($data);
        foreach ($data as $key) {
            # code...
            $inspector_ids = GroupTeam::find($key->id)->inspector_ids;

            if ($inspector_ids) {
                // Split the inspector_ids column into an array
                $inspectorIds = explode(',', $inspector_ids);

                // Count the number of inspectors
                $inspectorCount = count($inspectorIds);
            } else {
                $inspectorCount = 0; // Handle the case where the group is not found
            }
            $key['inspectorCount'] = $inspectorCount;
        }

        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
            ->addColumn('inspectorCount', function ($row) use ($id) {
                $inspector_ids = GroupTeam::where('group_id', $id)->where('id', $row->id)->first()->inspector_ids;
                if ($inspector_ids) {

                    $inspectorIds = explode(',', $inspector_ids);

                    // Count the number of inspectors
             
                    $count = count($inspectorIds);
                } else {
                    $count = 0;
                }
                if ($count == 1 || $count == 0) {

                    $btn = '<a class="btn btn-sm"   style="background-color: #F7AF15;">' . $count . '</a>';
                } else {
                    $btn = '<a class="btn btn-sm"   style="background-color: #274373; padding-inline: 15px;">' . $count . '</a>';
                }
                // dd($btn);
                return  $btn;
            })
            ->rawColumns(['action', 'inspectorCount'])
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
    public function store(Request $request, $id)
    {
        $messages = [
            'groupTeam_name.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'working_tree_id.required' => 'نظام العمل مطلوبة ولا يمكن تركها فارغة.',

        ];

        $request->validate([
            'groupTeam_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    // Check for uniqueness within the specific group_id
                    $exists = GroupTeam::where('name', $value)
                        ->where('group_id', $id)
                        ->exists();
                    if ($exists) {
                        $fail('هذا الاسم موجود بالفعل ضمن هذه المجموعة.');
                    }
                },
            ],
            'working_tree_id' => 'required|integer', // Assuming working_tree_id is required and is an integer
        ], $messages);
        // Validate the request input

        try {
            $grouptemItem = new GroupTeam();
            $grouptemItem->name = $request->groupTeam_name;
            $grouptemItem->group_id = $id;
            $grouptemItem->working_tree_id = $request->working_tree_id;
            $grouptemItem->save();

            return redirect()->back()->with('success', 'تم الاضافة بنجاح');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }



    public function team($id)
    {
        $arrayInspector = [];
        $inspector = Inspector::where('group_id', $id)->get();
        foreach ($inspector as $item) {
            $check = GroupTeam::where('group_id', $id)
                ->whereRaw('find_in_set(?, inspector_ids)', [$item->id])
                ->exists();
            if (!$check) {
                $arrayInspector[] = $item;
            }
        }

        // At this point, $arrayInspector contains the inspectors that do not exist in the inspector_ids column of GroupTeam
        $data = $arrayInspector;


        // dd($group);
        if ($inspector) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $team = GroupTeam::find($id);
        $group_id = $team->group_id;
        $workTrees  = WorkingTree::all();
        $inspectors = Inspector::where('group_id', $team->group_id)->orwhereNull('group_id')->get();
        $inspectorGroups = collect();
        $selectedInspectors = [];
        foreach ($inspectors as $inspector) {
            $groupTeams = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])->where('id', $id)->get();
            $check = GroupTeam::where('group_id', $group_id)
                ->whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])
                ->exists();
            if (!$check) {

                $inspector_ids = GroupTeam::where('group_id', $group_id)->where('id', $id)->first()->inspector_ids;
                $selectedInspectors = explode(',', $inspector_ids);
                $groupTeamIds = $groupTeams->pluck('id', 'name')->toArray();

                $inspectorGroups->push([
                    'inspector_id' => $inspector,
                    'group_team_ids' => $groupTeamIds
                ]);
            } else {
                $check = GroupTeam::where('group_id', $group_id)->where('id', $id)
                    ->whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])
                    ->exists();
                if ($check) {
                    $inspector_ids = GroupTeam::where('group_id', $group_id)->where('id', $id)->first()->inspector_ids;
                    $selectedInspectors = explode(',', $inspector_ids);
                    $groupTeamIds = $groupTeams->pluck('id', 'name')->toArray();

                    $inspectorGroups->push([
                        'inspector_id' => $inspector,
                        'group_team_ids' => $groupTeamIds
                    ]);
                }
            }
        }

        $allteams = GroupTeam::where('group_id', $team->group_id)->get();


        return view('groupteam.edit', compact('team', 'inspectorGroups', 'allteams', 'group_id', 'id', 'selectedInspectors', 'workTrees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show($id)
    {

        $team = GroupTeam::with('group')->with('working_tree')->where('id', $id)->first();
        $inspectorIds = explode(',', $team->inspector_ids);
        $inspectors = Inspector::whereIn('id', $inspectorIds)->get();
        $group_id = $team->group_id;

        return view('groupteam.showdetails', compact('team', 'inspectors', 'group_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Custom validation messages
        $messages = [
            'name.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'working_tree_id.required' => 'نظام العمل مطلوبة ولا يمكن تركها فارغة.',


        ];

        // Validate the request input
        $request->validate([
            'name' => 'required|string',
            'working_tree_id' => 'required',


        ], $messages);

        $team = GroupTeam::find($id);

        if (!$team) {
            return redirect()->back()->withErrors(['team_not_found' => 'فريق العمل غير موجود.']);
        }


        $newName = $request->name;
        $newInspectors = $request->inspectors_ids ? (array) $request->inspectors_ids : [];
        $oldInspectorIds = $team->inspector_ids ? explode(',', $team->inspector_ids) : [];

        // Ensure all IDs are strings for comparison
        $newInspectors = array_map('strval', $newInspectors);
        $oldInspectorIds = array_map('strval', $oldInspectorIds);

        $changeArr = array_diff($newInspectors, $oldInspectorIds);

        // Also check for removed inspector IDs
        $removedArr = array_diff($oldInspectorIds, $newInspectors);

        if (empty($changeArr) && empty($removedArr) && $team->name === $newName && $team->working_tree_id == $request->working_tree_id) {
            return redirect()->back()->withErrors(['nothing_updated' => 'لم يتم تحديث أي بيانات.']);
        }

        // Update the team name
        $team->name = $newName;
        $team->working_tree_id = $request->working_tree_id;

        // Update inspector_ids if provided
        if (!empty($newInspectors)) {
            $team->inspector_ids = implode(",", $newInspectors);
        } else {
            // If no inspectors are provided, clear the inspector_ids
            $team->inspector_ids = '';
        }

        // Save the changes
        $team->save();

        return redirect()->route('groupTeam.index', $team->group_id)->with('success', 'تم التعديل بنجاح');
    }

    // public function update(Request $request, $id)
    // {
    //     // Custom validation messages
    //     $messages = [
    //         'name.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
    //     ];

    //     // Validate the request input
    //     $request->validate([
    //         'name' => 'required|string',
    //     ], $messages);
    //     $team = GroupTeam::find($id);
    //     $newName = $request->name;
    //     $newInspectors = $request->inspectors_ids;
    //     if (!$newInspectors) {
    //         $newInspectors = $team->inspector_ids;
    //     }
    //     $oldInspectorIds = explode(',', $team->inspector_ids);
    //     $changeArr = array_diff($newInspectors, $oldInspectorIds);
    //     if (empty($changeArr)) {
    //         $changeArr = false;
    //     }
    //     $hasChanges = $team->name !== $newName || $changeArr;

    //     if (!$hasChanges) {
    //         return redirect()->back()->withErrors(['nothing_updated' => 'لم يتم تحديث أي بيانات.']);
    //     }

    //     if ($team) {
    //         // Update the team name
    //         $team->name = $newName;

    //         // Clear inspector_ids if the name is updated
    //         if ($request->has('name')) {
    //             $team->inspector_ids = '';
    //         }

    //         // Save the changes
    //         $team->save();

    //         // If inspector_ids are provided in the request
    //         if ($request->has('inspectors_ids')) {
    //             $inspector_ids = implode(",", $request->inspectors_ids);
    //             // foreach ($request->inspectors_ids as $index => $value) {
    //             //     $check = GroupTeam::where('group_id', $team->group_id)
    //             //         ->whereRaw('find_in_set(?, inspector_ids)', [$value]);

    //             //     if ($check->clone()->exists()) {
    //             //         $old = GroupTeam::find($check->clone()->first()->id);
    //             //         $oldInspectorIds = explode(',', $old->inspector_ids);
    //             //         $updatedOldInspectorIds = array_diff($oldInspectorIds, [$value]);
    //             //         $old->inspector_ids = implode(',', $updatedOldInspectorIds);
    //             //         $old->save();
    //             //     }
    //             // }

    //             $team->inspector_ids = $inspector_ids;
    //             $team->save();
    //         }
    //     }

    //     return redirect()->route('groupTeam.index', $team->group_id)->with('success', 'تم التعديل بنجاح');
    // }

    public function updateTransfer(Request $request, $group_id)
    {
        // Get the inspectors' IDs and team IDs from the request
        $inspectorIds = $request->inspectors_ids;
        $teams = $request->team_id;

        // Iterate over each inspector ID
        foreach ($inspectorIds as $inspectorId) {
            $newTeamId = $teams[$inspectorId];
            $currentGroup = GroupTeam::where('group_id', $group_id)->where('id', $newTeamId)->first();

            // Check if the inspector is already in a different group
            $existingGroupQuery = GroupTeam::where('group_id', $group_id)
                ->whereRaw('find_in_set(?, inspector_ids)', [$inspectorId]);

            if ($existingGroupQuery->exists()) {
                $existingGroup = $existingGroupQuery->first();

                if ($existingGroup->id != $newTeamId) {
                    // Remove inspector from the old group
                    $oldInspectorIds = explode(',', $existingGroup->inspector_ids);
                    $updatedOldInspectorIds = array_diff($oldInspectorIds, [$inspectorId]);
                    $existingGroup->inspector_ids = implode(',', $updatedOldInspectorIds);
                    $existingGroup->save();

                    // Add inspector to the new group
                    if (empty($currentGroup->inspector_ids)) {
                        $currentGroup->inspector_ids = $inspectorId;
                    } else {
                        $currentInspectorIds = explode(',', $currentGroup->inspector_ids);
                        $currentInspectorIds[] = $inspectorId;
                        $currentGroup->inspector_ids = implode(',', $currentInspectorIds);
                    }
                    $currentGroup->save();
                }
            } else {
                // Add inspector to the new group if not already assigned
                if (empty($currentGroup->inspector_ids)) {
                    $currentGroup->inspector_ids = $inspectorId;
                } else {
                    $currentInspectorIds = explode(',', $currentGroup->inspector_ids);
                    $currentInspectorIds[] = $inspectorId;
                    $currentGroup->inspector_ids = implode(',', $currentInspectorIds);
                }
                $currentGroup->save();
            }
        }

        return redirect()->route('groupTeam.index', $group_id)->with('success', 'تم التعديل بنجاح');
    }

    public  function transfer($group_id)
    {
        $selectedInspectors = [];

        $inspectors = Inspector::with('user')->where('group_id', $group_id)->get();
        $inspectorGroups = collect();
        foreach ($inspectors as $inspector) {
            $groupTeams = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])->get();
            $GroupTeamGets = GroupTeam::where('group_id', $group_id)->get();
            foreach ($GroupTeamGets as $GroupTeamGet) {
                $inspectorIds = explode(',', $GroupTeamGet->inspector_ids);
                $selectedInspectors = array_merge($selectedInspectors, $inspectorIds);
            }
            $groupTeamIds = $groupTeams->pluck('id', 'name')->toArray();

            $inspectorGroups->push([
                'inspector_id' => $inspector,
                'group_team_ids' => $groupTeamIds
            ]);
        }
        $allteams = GroupTeam::where('group_id', $group_id)->get();
        // dd($groupTeams);
        return view('groupteam.transfer', compact('inspectorGroups', 'allteams', 'group_id', 'selectedInspectors'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupTeam $groupTeam)
    {
        //
    }
}
