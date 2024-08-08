<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\instantmission;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreinstantmissionRequest;
use App\Http\Requests\UpdateinstantmissionRequest;

class InstantmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('instantMissions.view');
    }

    public function getInstantMission()
    {
        $data = instantmission::all();

        return DataTables::of($data)->addColumn('action', function ($row) {

            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
            ->addColumn('group_id', function ($row) { // New column for departments count
                $group_id = Groups::where('id', $row->group_id)->pluck('name')->first();
                return $group_id;
            })
            ->addColumn('group_team_id', function ($row) { // New column for departments count

                $group_team_id = GroupTeam::where('id', $row->group_team_id)->pluck('name')->first();
                return $group_team_id;
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Groups::all();
        $groupTeams = GroupTeam::all();
        return view('instantMissions.create', compact('groups', 'groupTeams'));
    }
    // getGroups
    public function getGroups($id)
    {
        // $groups = Groups::all();
        $groupTeams = GroupTeam::where('group_id', $id)->get();
        return response()->json($groupTeams);
        // return view('instantMissions.create',compact('groups','groupTeams'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $messages = [
            'label.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
            'group_team_id.required' => ' الفرقة  مطلوب ولا يمكن تركه فارغاً.',
            'group_id.required' => ' المجموعة مطلوب ولا يمكن تركه فارغاً.',
            // Add more custom messages here
        ];

        $validatedData = Validator::make($request->all(), [
            'label' => 'required|string',
            'group_team_id' => 'required',
            'group_id' => 'required',
        ], $messages);

        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        $new = new instantmission();
        $new->label = $request->label;
        $new->location = $request->location;
        $new->group_id = $request->group_id;
        $new->group_team_id = $request->group_team_id;
        $new->description = $request->description;
        $new->save();

        if ($request->hasFile('images')) {
            $file = $request->images;
            $path = 'instantMission/images';
            // foreach ($file as $image) {
            //     // $this->UploadFilesWithoutReal('images', 'image', new Image, $image);
            UploadFilesIM($path, 'attachment', $new, $file);
            // }

        }
        return view('instantMissions.view');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $IM = instantmission::find($id);
        $groups = Groups::all();
        $groupTeams = GroupTeam::where('group_id', $IM->group_id)->get();
        return view('instantMissions.show', compact('groups', 'groupTeams', 'IM'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $IM = instantmission::find($id);
        $groups = Groups::all();
        $groupTeams = GroupTeam::where('group_id', $IM->group_id)->get();
        return view('instantMissions.edit', compact('groups', 'groupTeams', 'IM'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        // dd($request);
        // Validate request data
        $messages = [
            'label.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'group_team_id.required' => 'الفرقة مطلوبة ولا يمكن تركها فارغة.',
            'group_id.required' => 'المجموعة مطلوبة ولا يمكن تركها فارغة.',
        ];

        $validatedData = $request->validate([
            'label' => 'required|string',
            'group_team_id' => 'required',
            'group_id' => 'required',
        ], $messages);
        $instantmission = instantmission::find($id);
        // Update the instantmission record
        $instantmission->label = $request->label;
        $instantmission->location = $request->location;
        $instantmission->group_id = $request->group_id;
        $instantmission->group_team_id = $request->group_team_id;
        $instantmission->description = $request->description;
        $instantmission->save();

        // Handle file uploads
        if ($request->hasFile('images')) {
            $files = $request->images;
            $path = 'instantMission/images';

            // Assuming UploadFilesIM is a helper function to handle the file upload
            UploadFilesIM($path, 'attachment', $instantmission, $files);
        }

        return view('instantMissions.view')->with('success', 'Mission updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(instantmission $instantmission)
    {
        //
    }
}