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
    public function index()
    {
        //
        return view('groupteam.show');
    }
    public function getGroupTeam()
    {
        $data = GroupTeam::all();
        // dd($data);

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
    public function store(Request $request)
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
            $inspector_ids = implode(",", $request->inspectors_ids);


            $grouptemItem = new GroupTeam();
            $grouptemItem->name = $request->groupTeam_name;
            $grouptemItem->group_id = $request->group_id;
            $grouptemItem->inspector_ids = $inspector_ids;
            $grouptemItem->save();
            $workTimes = WorkingTime::all();

            return view('group.view', compact('workTimes'))->with('success', 'تم الاضافة بنجاح');
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
    public function show($id)
    {
        $team = GroupTeam::find($id);
        $inspectors = Inspector::where('group_id',$team->group_id)->get();
        $inspectorGroups = collect();
        foreach ($inspectors as $inspector) {
            $groupTeams = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])->get();
            $groupTeamIds = $groupTeams->pluck('id','name')->toArray();

            $inspectorGroups->push([
                'inspector_id' => $inspector,
                'group_team_ids' => $groupTeamIds
            ]);
            
            
        }

        $allteams = GroupTeam::where('group_id',$team->group_id)->get();

        // dd($inspectorGroups);

        return view('groupteam.edit',compact('team' ,'inspectorGroups','allteams'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GroupTeam $groupTeam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupTeamRequest $request, GroupTeam $groupTeam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupTeam $groupTeam)
    {
        //
    }
}
