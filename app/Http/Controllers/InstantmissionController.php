<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use Illuminate\Http\Request;
use App\Events\MissionCreated;
use App\Models\instantmission;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Event;
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
            ->addColumn('locationLink', function ($row) { // New column for departments count

                return '<a href="'.$row->location.'" target="_blank" style="color:blue !important;">رابط</a>';
            })

            ->rawColumns(['action', 'locationLink'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Groups::all();
        $groupTeams = GroupTeam::all();
        $inspectors = Inspector::all();
        return view('instantMissions.create', compact('groups', 'groupTeams','inspectors'));
    }
    // getGroups
    public function getGroups($id)
    {
        // $groups = Groups::all();
        $groupTeams = GroupTeam::where('group_id', $id)->get();
        return response()->json($groupTeams);
        // return view('instantMissions.create',compact('groups','groupTeams'));
    }
    public function getInspector($team_id,$group_id)
    {
        // $groups = Groups::all();
        $team = GroupTeam::where('group_id', $group_id)->where('id', $team_id)->first();
        $inspectorIds = explode(',', $team->inspector_ids);

        // Retrieve the inspectors based on the ids
        $inspectors = Inspector::whereIn('id', $inspectorIds)->get();

        // dd($team);
        // $inspectors = Inspector::whereIn('id',$team->inspector_ids)->get();
        
        return response()->json($inspectors);
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
            'location.required' => ' الموقع مطلوب ولا يمكن تركه فارغاً.',
            // Add more custom messages here
        ];

        $validatedData = Validator::make($request->all(), [
            'label' => 'required|string',
            'group_team_id' => 'required',
            'group_id' => 'required',
            'location' => 'required',
        ], $messages);

        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        $coordinates = getLatLongFromUrl($request->location);
        if($coordinates == null)
        {
            $lat= null;
            $long= null;
        }
        else
        {
            $lat = $coordinates["latitude"];
            $long = $coordinates["longitude"];
        }
        $new = new instantmission();
        $new->label = $request->label;
        $new->location = $request->location;
        $new->group_id = $request->group_id;
        $new->group_team_id = $request->group_team_id;
        $new->inspector_id = $request->inspectors;
        $new->description = $request->description;
        $new->latitude = $lat;
        $new->longitude = $long;
        $new->save();

        if ($request->hasFile('images')) {
            $file = $request->images;
            $path = 'instantMission/images';
            // foreach ($file as $image) {
            //     // $this->UploadFilesWithoutReal('images', 'image', new Image, $image);
            UploadFilesIM($path, 'attachment', $new, $file);
            // }

        }
        // ;
        $results = Event::dispatch(new MissionCreated($new));
        // $results = event(new MissionCreated($new));
// dd($results);
        if (!empty($results) && in_array(true, $results, true)) {
            return redirect()->route('instant_mission.index')->with('message', "تمت اضافة المهمة بنجاح");
        } else {
            // Handle specific errors based on listener responses
            return redirect()->route('instant_mission.index')->with('message', "خطأ ");
        }
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

        $coordinates = getLatLongFromUrl($request->location);
        $instantmission = instantmission::find($id);
        // Update the instantmission record
        $instantmission->label = $request->label;
        $instantmission->location = $request->location;
        $instantmission->group_id = $request->group_id;
        $instantmission->group_team_id = $request->group_team_id;
        $instantmission->description = $request->description;
        $instantmission->latitude = $coordinates["latitude"];
        $instantmission->longitude = $coordinates["longitude"];
        $instantmission->save();

        // Handle file uploads
        // if ($request->hasFile('images') && $request->remaining_files != null) {


        $files = [];

        // dd(explode(',',$request->remaining_files));
        // Check if 'images' are uploaded and merge them into the $files array
        if ($request->hasFile('images')) {
            $files = array_merge($files, $request->file('images'));
        }

        // Check if 'remaining_files' are present and merge them into the $files array
        if ($request->remaining_files != null) {
            $files = array_merge($files, explode(',', $request->remaining_files));
        }

        // dd($request->file('images'));
        // $path = 'instantMission/images';

        // $files = $files;
        $path = 'instantMission/images';

        // Assuming UploadFilesIM is a helper function to handle the file upload
        UploadFilesIM($path, 'attachment', $instantmission, $files);
        // }

        return redirect()->route('instant_mission.index')->with('success', 'Mission updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(instantmission $instantmission)
    {
        //
    }
}
