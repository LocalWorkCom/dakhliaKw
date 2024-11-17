<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use Illuminate\Http\Request;
use App\Events\MissionCreated;
use App\Models\instantmission;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreinstantmissionRequest;
use App\Http\Requests\UpdateinstantmissionRequest;
use App\Models\Attendance;
use App\Models\Violation;

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
            ->addColumn('group_id', function ($row) {
                $group_id = Groups::where('id', $row->group_id)->pluck('name')->first();
                return $group_id;
            })
            ->addColumn('group_team_id', function ($row) {

                $group_team_id = GroupTeam::where('id', $row->group_team_id)->pluck('name')->first();
                return $group_team_id;
            })
            ->addColumn('locationLink', function ($row) {

                return '<a href="' . $row->location . '" target="_blank" style="color:blue !important;">رابط</a>';
            })
            ->addColumn('attendance', function ($row) {
                $attendance = Attendance::where('instant_id', $row->id)->where('flag', 1)->count();
                return '<a href="' . route('instant_mission.getAttendance', ['id' => $row->id]) . '" style="color:blue !important;">' . $attendance . '</a>';
            })->addColumn('violaions', function ($row) {
                $violations = Violation::where('mission_id', $row->id)->where('status', 1)->count();

                return '<a href="' . route('instant_mission.getViolations', ['id' => $row->id]) . '" style="color:blue !important;">' . $violations . '</a>';
            })
            ->rawColumns(['action', 'locationLink', 'attendance', 'violaions'])
            ->make(true);
    }


    public function getAttendance($id)
    {
        $title = "سجل الحضور";
        $data = Attendance::with([
            'employees.force',
            'employees.grade',
            'employees.type'
        ])
        ->where('instant_id', $id)
        ->where('flag', 1)
        ->get();

       //dd($data);
        return view('instantMissions.attendance', compact('data', 'title'));
    }
    public function getViolations($id)
    {
        $title = "سجل مخالفات";
        $data = Violation::leftJoin('grades', 'grades.id', '=', 'violations.grade')
            ->leftJoin('inspector_mission', 'inspector_mission.id', '=', 'violations.mission_id')
            ->leftJoin('users', 'users.id', '=', 'violations.user_id')
            ->leftJoin('violation_type', 'violation_type.id', '=', 'violations.civil_type')
            ->selectRaw("
                    violations.image,
                    violations.id,
                    violations.military_number,
                    violations.Civil_number,
                    violations.grade,
                    users.name as user_name,
                    violations.mission_id,
                    violations.file_num,
                    violations.description,
                    violations.civil_type,
                    violation_type.name as civil_type_name,
                    CONCAT_WS('/', violations.name, grades.name) as name,
                    (
                        SELECT GROUP_CONCAT(violation_type.name)
                        FROM violation_type
                        WHERE FIND_IN_SET(violation_type.id, violations.violation_type)
                    ) AS ViolationType,
                    IF(violations.flag = 1, 'مخالفة سلوك انظباطي', 'مخالفة مباني') as Type,
                    grades.name as grade_name
                ")
            ->where('violations.mission_id', $id)
            ->where('violations.status', 1)
            ->get();


        // dd($data);
        return view('instantMissions.violationDetails', compact('data', 'title'));
    }
    public function create()
    {

        $inspectors_group = Inspector::whereHas('user', function ($query) {
            $query->where('department_id', Auth::user()->department_id);
        })
            ->select('group_id')
            ->groupBy('group_id')
            ->pluck('group_id');
        // dd(Auth::user()->rule->name);
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $groups = Groups::all();
            $groupTeams = GroupTeam::all();
            $inspectors = Inspector::all();
        } else {
            $groups = Groups::whereIn('id', $inspectors_group)->get();
            // dd($groups);
            $groupTeams = GroupTeam::whereIn('group_id', $inspectors_group)->get();
            $userDepartmentId = Auth::user()->department_id;
            $inspectors = Inspector::with('user')->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })->get();
        }


        //  dd($groups);


        return view('instantMissions.create', compact('groups', 'groupTeams', 'inspectors'));
    }
    // getGroups
    public function getGroups($id)
    {
        // $groups = Groups::all();
        if ($id != -1)
            $groupTeams = GroupTeam::where('group_id', $id)->get();
        else   $groupTeams = GroupTeam::all();
        return response()->json($groupTeams);
        // return view('instantMissions.create',compact('groups','groupTeams'));
    }
    public function getInspector($team_id, $group_id)
    {
        // $groups = Groups::all();
        $team = GroupTeam::whereNotNull('created_at');
        if ($group_id != -1) {
            $team->where('group_id', $group_id);
            if ($team_id != -1) $team->where('id', $team_id)->get();
        } else {
            if ($team_id != -1) $team->where('id', $team_id)->get();
        }
        $team = $team->get();
        /*  echo $group_id."<br>";
       dd($team_id); */
        // $team = GroupTeam::where('group_id', $group_id)->where('id', $team_id)->first();
        $inspector_ids = '';
        foreach ($team as $team) {
            if ($inspector_ids != '') $inspector_ids .= ',';
            $inspector_ids .= $team->inspector_ids;
        }
        // dd($team);
        $inspectorIds = explode(',', $inspector_ids);

        // Retrieve the inspectors based on the ids
        $inspectors = Inspector::whereIn('id', $inspectorIds)->get();

        // dd($team);
        // $inspectors = Inspector::whereIn('id',$team->inspector_ids)->get();

        return response()->json($inspectors);
        // return view('instantMissions.create',compact('groups','groupTeams'));
    }

    public function store(Request $request)
    {
        $messages = [
            'label.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'group_team_id.required' => 'الفرقة مطلوبة ولا يمكن تركها فارغة.',
            'group_id.required' => 'المجموعة مطلوبة ولا يمكن تركها فارغة.',
            'location.required' => 'الموقع مطلوب ولا يمكن تركه فارغاً.',
            'date.required' => 'التاريخ مطلوب ولا يمكن تركه فارغاً.',
            'date.after_or_equal' => 'التاريخ يجب أن يكون اليوم أو في المستقبل.', // Custom message for date validation
            // Add more custom messages here
        ];

        $validatedData = Validator::make($request->all(), [
            'label' => 'required|string',
            'group_team_id' => 'required',
            'group_id' => 'required',
            'location' => 'required',
            'date' => 'required|date|after_or_equal:today', // Validate that the date is today or in the future
        ], $messages);

        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        $coordinates = getLatLongFromUrl($request->location);
        // dd($coordinates);
        if ($coordinates == null) {
            $lat = null;
            $long = null;
        } else {
            $lat = $coordinates["latitude"];
            $long = $coordinates["longitude"];
        }
        $new_1 = new instantmission();
        $new_1->label = $request->label;
        $new_1->location = $request->location;
        $new_1->group_id = $request->group_id;
        $new_1->group_team_id = $request->group_team_id;
        $new_1->inspector_id = $request->inspectors;
        $new_1->description = $request->description;
        $new_1->latitude = $lat;
        $new_1->longitude = $long;
        $new_1->date = $request->date;
        $new_1->created_departement = auth()->user()->department_id;

        $new_1->save();
       //dd($request);
        if ($request->file('images')) {
            $file = $request->images;
            $path = 'instantMission/images';
            // foreach ($file as $image) {
            //     // $this->UploadFilesWithoutReal('images', 'image', new Image, $image);
            UploadFilesIM($path, 'attachment', $new_1, $file);
            // }

        }
        $results = Event::dispatch(new MissionCreated($new_1));
        // $results = event(new MissionCreated($new_1));
        if (!empty($results) && in_array(true, $results, true)) {
            return redirect()->route('instant_mission.index')->with('message', "تمت اضافة المهمة بنجاح");
        } else {
            // Handle specific errors based on listener responses
            return redirect()->route('instant_mission.index')->with('message', "خطأ ");
        }
    }
    public function show($id)
    {
        $IM = instantmission::find($id);
        $groups = Groups::all();
        $userDepartmentId = Auth::user()->department_id;
        $inspectors_group = Inspector::with('user')->where('flag', 0)
            ->whereHas('user', function ($query) use ($userDepartmentId) {
                $query->where('department_id', $userDepartmentId);
            })->select('group_id')
            ->groupBy('group_id')->pluck('group_id');
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $groups = Groups::all();
            $groupTeams = GroupTeam::all();
            $inspectors = Inspector::all();
        } else {
            $groups = Groups::whereIn('id', $inspectors_group)->get();
            // dd($groups);
            $groupTeams = GroupTeam::whereIn('group_id', $inspectors_group)->get();
            $userDepartmentId = Auth::user()->department_id;
            $inspectors = Inspector::with('user')->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })->get();
        }
        $groupTeams = GroupTeam::where('group_id', $IM->group_id)->get();

        $inspectors = Inspector::where('id', $IM->inspector_id)->get();

        return view('instantMissions.show', compact('groups', 'groupTeams', 'inspectors', 'IM'));
    }
    public function edit($id)
    {
        $IM = instantmission::find($id);
        $userDepartmentId = Auth::user()->department_id;
        $inspectors_group = Inspector::with('user')->where('flag', 0)
            ->whereHas('user', function ($query) use ($userDepartmentId) {
                $query->where('department_id', $userDepartmentId);
            })->select('group_id')
            ->groupBy('group_id')->pluck('group_id');
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $groups = Groups::all();
            $groupTeams = GroupTeam::all();
            $inspectors = Inspector::all();
        } else {

            $groups = Groups::whereIn('id', $inspectors_group)->get();
            // dd($groups);
            $groupTeams = GroupTeam::whereIn('group_id', $inspectors_group)->get();
            $userDepartmentId = Auth::user()->department_id;
            $inspectors = Inspector::with('user')->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })->get();
        }

        // $groups = Groups::all();
        // $groupTeams = GroupTeam::where('group_id', $IM->group_id)->get();
        return view('instantMissions.edit', compact('groups', 'groupTeams', 'IM'));
    }

    public function update(Request $request, $id)
    {

        // dd($request);
        // Validate request data
        $messages = [
            'label.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'group_team_id.required' => 'الفرقة مطلوبة ولا يمكن تركها فارغة.',
            'group_id.required' => 'المجموعة مطلوبة ولا يمكن تركها فارغة.',
            'location.required' => 'الموقع مطلوب ولا يمكن تركه فارغاً.',
            'date.required' => 'التاريخ مطلوب ولا يمكن تركه فارغاً.',
            'date.after_or_equal' => 'التاريخ يجب أن يكون اليوم أو في المستقبل.', // Custom message for date validation
            // Add more custom messages here
        ];

        $validatedData = Validator::make($request->all(), [
            'label' => 'required|string',
            'group_team_id' => 'required',
            'group_id' => 'required',
            'location' => 'required',
            'date' => 'required|date|after_or_equal:today', // Validate that the date is today or in the future
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

    public function destroy(instantmission $instantmission)
    {
        //
    }
}
