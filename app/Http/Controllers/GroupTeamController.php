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

class GroupTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        //
        return view('groupteam.show', compact('id'));
    }
    public function getGroupTeam($id)
    {
        $data = GroupTeam::with('group')->where('group_id', $id)->get();
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
    public function store(Request $request, $id)
    {
        // dd($request);
        // $messages = [
        //     'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
        //     'group_id.required' => 'بداية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
        //     'inspector_ids.required' => 'نهاية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',

        // ];
        // $validatedData = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'group_id' => 'required',
        //     'inspector_ids' => 'required',

        // ], $messages);

        // // Handle validation failure
        // if ($validatedData->fails()) {
        //     return redirect()->back()->withErrors($validatedData)->withInput();
        // }
        try {
            // $inspector_ids = implode(",", $request->inspectors_ids);


            $grouptemItem = new GroupTeam();
            $grouptemItem->name = $request->groupTeam_name;
            $grouptemItem->group_id = $id;
            // $grouptemItem->inspector_ids = $inspector_ids;
            $grouptemItem->save();

            return redirect()->back()->with('success', 'تم الاضافة بنجاح');
            // return view('group.view', compact('workTimes'))->with('success', 'تم الاضافة بنجاح');
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
        $inspectors = Inspector::where('group_id', $team->group_id)->orwhereNull('group_id')->get();
        $inspectorGroups = collect();
        foreach ($inspectors as $inspector) {
            $groupTeams = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])->get();
            $inspector_ids = GroupTeam::where('group_id', $group_id)->where('id', $id)->first()->inspector_ids;
            $selectedInspectors = explode(',', $inspector_ids);


            $groupTeamIds = $groupTeams->pluck('id', 'name')->toArray();

            $inspectorGroups->push([
                'inspector_id' => $inspector,
                'group_team_ids' => $groupTeamIds
            ]);
        }

        $allteams = GroupTeam::where('group_id', $team->group_id)->get();


        return view('groupteam.edit', compact('team', 'inspectorGroups', 'allteams', 'group_id', 'id', 'selectedInspectors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(GroupTeam $groupTeam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $team = GroupTeam::find($id);
        $inspector_ids = implode(",", $request->inspectors_ids);
        foreach ($request->inspectors_ids as $key) {
            $inspector = Inspector::find($key);
            $inspector->group_id = $team->group_id;
            $inspector->save();
        }
        $team->inspector_ids = $inspector_ids;
        $team->save();
        return redirect()->route('groupTeam.index', $team->group_id)->with('success', 'تم التعديل بنجاح');
    }
    public function updateTransfer(Request $request, $group_id)
    {

        // Get the inspectors' IDs from the request
        $inspectorIds = $request->inspectors_ids;

        foreach ($inspectorIds as $index => $value) {
            $teams = $request->team_id;
            $currentGroup = GroupTeam::where('group_id', $group_id)->where('id', $teams[$index])->first();

            // Check if the inspector exists in the current group
            $check = GroupTeam::where('group_id', $group_id)
                ->whereRaw('find_in_set(?, inspector_ids)', [$value]);
            if ($check->clone()->exists()) {
                // dd($teams[$index]);
                if ($check->clone()->first()->id != $teams[$index]) {

                    $old =   GroupTeam::find($check->clone()->first()->id);
                    $oldInspectorIds = explode(',', $old->inspector_ids);
                    $updatedOldInspectorIds = array_diff($oldInspectorIds, [$value]);
                    $old->inspector_ids = implode(',', $updatedOldInspectorIds);
                    $old->save();

                    if (empty($currentGroup->inspector_ids)) {
                        $updatedCurrentInspectorIds = implode(",", $inspectorIds);
                    } else {
                        $currentInspectorIds = explode(',', $currentGroup->inspector_ids);
                        $updatedCurrentInspectorIds = implode(',', array_diff($currentInspectorIds, [$value]));
                    }
                    // Remove the inspectors from the current group
                    $currentGroup->inspector_ids = $updatedCurrentInspectorIds;
                    $currentGroup->save();
                }
            } else {
                if (empty($currentGroup->inspector_ids)) {
                    $updatedCurrentInspectorIds = implode(",", $inspectorIds);
                } else {
                    $currentInspectorIds = explode(',', $currentGroup->inspector_ids);
                    $updatedCurrentInspectorIds = implode(',', array_diff($currentInspectorIds, [$value]));
                }
                // Remove the inspectors from the current group
                $currentGroup->inspector_ids = $updatedCurrentInspectorIds;
                $currentGroup->save();
            }
        }
        return redirect()->route('groupTeam.index', $group_id)->with('success', 'تم التعديل بنجاح');
    }
    public  function transfer($group_id)
    {
        $inspectors = Inspector::where('group_id', $group_id)->get();
        $inspectorGroups = collect();
        foreach ($inspectors as $inspector) {
            $groupTeams = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])->get();
            $inspector_ids = GroupTeam::where('group_id', $group_id)->first()->inspector_ids;
            $selectedInspectors = explode(',', $inspector_ids);


            $groupTeamIds = $groupTeams->pluck('id', 'name')->toArray();

            $inspectorGroups->push([
                'inspector_id' => $inspector,
                'group_team_ids' => $groupTeamIds
            ]);
        }
        $allteams = GroupTeam::where('group_id', $group_id)->get();

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
