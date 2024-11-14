<?php

namespace App\Http\Controllers;

use App\Models\departements;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\Rule;
use App\Models\User;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class InspectorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $userDepartmentId = $user->department_id;

        if ($user->rule->name == "localworkadmin" || $user->rule->name == "superadmin") {
            $all = Inspector::where('flag', 0)->count();
            $assignedInspectors = Inspector::whereNotNull('group_id')->where('flag', 0)->count();
            $unassignedInspectors = Inspector::whereNull('group_id')->where('flag', 0)->count();
        } else {
            // Ensure manager cannot see inspectors in their own department
            $all = Inspector::with('user')->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })
                ->count();

            $assignedInspectors = Inspector::with('user')->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })
                ->whereNotNull('group_id')
                ->count();

            $unassignedInspectors = Inspector::with('user')->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })
                ->whereNull('group_id')
                ->count();
        }

        return view('inspectors.index', compact('assignedInspectors', 'unassignedInspectors', 'all'));
    }

    public function addToGroup(Request $request)
    {
        //dd($request);
        // Find all teams that contain this inspector
        $teams =  GroupTeam::where('group_id', $request->group_id)
            ->where(function ($query) use ($request) {
                $query->where('inspector_ids', 'LIKE', '%' . $request->id . '%');
            })
            ->get();



        foreach ($teams as $team) {
            // Split the comma-separated string into an array
            $inspectorIds = explode(',', $team->inspector_ids);

            // Remove the inspector from the array
            if (($key = array_search($request->id, $inspectorIds)) !== false) {
                unset($inspectorIds[$key]);
            }

            // Re-join the array into a comma-separated string and save the team
            $team->inspector_ids = implode(',', $inspectorIds);
            $team->save();
        }

        // Update the inspector's group
        $inspector = Inspector::findOrFail($request->id);
        $inspector->group_id = $request->group_id;
        $inspector->save();

        return redirect()->route('inspectors.index')
            ->with('success', 'تم أضافه المفتش لمجموعه')
            ->with('showModal', true);
    }

    public function getInspectors()
    {

        $userDepartmentId = Auth::user()->department_id;
        $userRole = Auth::user()->rule->name;

        if ($userRole == "localworkadmin" || $userRole == "superadmin") {
            $data = Inspector::with('user')->where('flag', 0)->orderBy('id', 'desc');
        } else {
            $data = Inspector::with('user')->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })
                ->orderBy('id', 'desc');
        }

        $filter = request('filter');

        if ($filter == 'assigned') {
            $data->whereNotNull('group_id');
        } elseif ($filter == 'unassigned') {
            $data->whereNull('group_id');
        }

        $data = $data->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $remove_permission = null;
            $group_permission = null ;
            if ($row->group_id !=  null) {
                $inspectorExists = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$row->id])->exists();
                $group_permission = !$inspectorExists
                    ? '<a class="btn btn-sm"  style="background-color: #7e7d7c;"  onclick="openAddModal(' . $row->id . ', ' . $row->group_id . ')">  <i class="fa fa-edit"></i> تعديل مجموعه</a>' : '';
                //$group_permission = '<a class="btn btn-sm"  style="background-color: #7e7d7c;"  onclick="openAddModal(' . $row->id . ', ' . $row->group_id . ')">  <i class="fa fa-edit"></i> تعديل مجموعه</a>';
                $remove_permission =  '<a class="btn btn-sm" style="background-color: #E3641E;" onclick="openTransferModal(' . $row->id . ')">
                <i class="fa-solid fa-user-tie"></i> تحويل لموظف
            </a>';
            } else {
                $group_permission = '<a class="btn btn-sm"  style="background-color: green;"  onclick="openAddModal(' . $row->id . ',0)">   <i class="fa fa-plus"></i> أضافه</a>';
            }
            $show_permission = '<a href="' . route('inspectors.show', $row->id) . '" class="btn btn-sm " style="background-color: #274373;">
                            <i class="fa fa-eye"></i>عرض</a>';
            $edit_permission =  '<a href="' . route('inspectors.edit', $row->id) . '" class="btn btn-sm"  style="background-color: #F7AF15;">
                                            <i class="fa fa-edit"></i> تعديل
                                        </a>';


            return  $show_permission . ' ' . $edit_permission . ' ' . $group_permission . ' ' . $remove_permission;
        })
            ->addColumn('name', function ($row) {
                return $row->user->name ? $row->user->name : 'لا يوجد أسم';
            })->addColumn('Id_number', function ($row) {
                return $row->user->Civil_number ? $row->user->Civil_number : 'لا يوجد رقم هويه';
            })
            ->addColumn('group_id', function ($row) {
                return $row->group_id ? $row->group->name : 'لا يوجد مجموعه للمفتش';
            })
            ->addColumn('position', function ($row) {
                return $row->user->grade->name ?? 'لا يوجد رتبه';
            })
            ->addColumn('phone', function ($row) {
                return $row->user->phone ?? 'لا يوجد هاتف';
            })
            ->addColumn('type', function ($row) {
                $types = [
                    'Buildings' => 'مفتش مباني',
                    'internbilding' => 'مفتش متدرب مباني',
                    'internslok' => 'مفتش متدرب سلوك انضباطي',
                    'slok' => 'مفتش سلوك انضباطي'
                ];
                return $types[$row->type] ?? 'مفتش سلوك انضباطي';
            })
            ->rawColumns(['action'])

            ->make(true);
    }

    public function create()
    {
        $departmentId = Auth::user()->department_id;

        $department = departements::find($departmentId);
        $departmentId = auth()->user()->department_id;
        $inspectorUserIds = Inspector::where('flag', 0)->pluck('user_id')->toArray();

        $allmangers = departements::whereNotNull('manger')->pluck('manger')->toArray();
        $userDepartmentId = Auth::user()->department_id;

        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $users = User::where('id', '!=', auth()->user()->id)
                ->whereNotIn('id', $inspectorUserIds)
                ->whereNotIn('id', $allmangers)
                ->get();
        } else {
            $users = User::with('department')
                ->join('departements', 'departements.id', '=', 'users.department_id') // Join the `departements` table
                ->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id) // Match user’s department
                        ->orWhere('departements.parent_id', Auth::user()->department_id); // Match department’s parent_id
                })
                ->where('users.id', '!=', $department->manger)
                ->where('users.id', '!=', auth()->user()->id)
                ->whereNotIn('users.id', $inspectorUserIds)
                ->select('users.*') // Ensure only `users` columns are selected
                ->get();
        }
        //dd($users);

        return view('inspectors.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function TransferToEmployee(Request $request)
    {
        $inspector = Inspector::find($request->id_employee);
        if (!$inspector) {
            return redirect()->back()
                ->with('error', 'تعذر الحصول على بيانات المفتش ')
                ->with('showModal', false);
        }

        $group = $inspector->group_id;
        $inspector->group_id = null;
        $inspector->flag = 1;
        $inspector->save();

        $value_remove = $request->id_employee;
        $groupTeams = GroupTeam::where('group_id', $group)->get();

        /*  foreach ($groupTeams as $currentGroup) {
            $currentInspectorIds = explode(',', $currentGroup->inspector_ids);

            // Remove the specified inspector ID from the group
            $updatedInspectorIds = array_filter($currentInspectorIds, function ($id) use ($value_remove) {
                return $id != $value_remove;
            });

            if (empty($updatedInspectorIds)) {
                $currentGroup->inspector_ids = null; // Set to NULL if no inspectors left
            } else {
                $currentGroup->inspector_ids = implode(',', $updatedInspectorIds); // Re-implode remaining IDs
            }

            // Save the updated group
            $currentGroup->save();
        } */

        // Delete future inspector missions for the removed inspector
        $inspectorMissions = InspectorMission::where('inspector_id', $request->id_employee)
            ->where('date', '>=', today())
            ->get();
        addInspectorHistory($request->id_employee, $group, $groupTeams[0]->id, 1);

        foreach ($inspectorMissions as $inspectorMission) {
            $inspectorMission->delete();
        }

        return redirect()->back()
            ->with('success', 'تم تحويل المفتش لموظف')
            ->with('showModal', true);
    }

    public function store(Request $request)
    {
        //  dd($request->all());

        $rules = [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
        ];

        // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم نقطه',
            'user_id.required' => 'يجب ادخال الموظف المراد تحويله لمفتش',
            'position.required' => 'يجب ادخال الرتبه',
            'Id_number.required' => 'يجب ادخال رقم الهويه ',
            'type.required' => 'يجب اختيار نوع المفتش',

        ];

        // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $rule = Rule::where('name', 'inspector')->first();
        $user = User::findOrFail($request->user_id);
        //dd($user->flag);
        if ($user->flag === "employee") {
            //  $user->flag = "user";
            $user->password =  Hash::make('123456');
            $user->rule_id = $rule->id;
            $user->save();
        } else {
            $user->flag = "employee";
            $user->rule_id = $rule->id;
            //  $user->password =  Hash::make('123456');
            $user->save();
        }

        $is_hashistory = Inspector::where('user_id', $request->user_id)->value('id');
        if ($is_hashistory) {
            $inspector = Inspector::findOrFail($is_hashistory);
            $inspector->flag = 0;
            $inspector->group_id = null;

            $inspector->save();
        } else {
            $inspector = new Inspector();
            $inspector->name = $user->name;
            $inspector->phone = $user->phone;
            //Buildings => مفتش مبانى  , internbilding =>مفتش متدرب مبانى   , internslok=> مفتس متدرب سلوك انضباطى   ,slok=>  مفتش سلوك
            $inspector->type = $request->type;

            $inspector->position = $user->position;

            $inspector->user_id = $request->user_id;
            $inspector->Id_number = $user->Civil_number;
            // $inspector->department_id = $user->department_id;
            $inspector->save();
        }


        //   dd($departements);
        return redirect()->route('inspectors.index')->with('success', 'Inspector created successfully.')->with('showModal', true);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $inspector = Inspector::findOrFail($id);
        $users = User::get();
        return view('inspectors.show', compact('inspector', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $inspector = Inspector::find($id);
        // dd($inspector);
        $departmentId = Auth::user()->department_id ? Auth::user()->department_id : null;
        $users = User::where('id', $inspector->id)->with('grade')->get();

        return view('inspectors.edit', compact('inspector', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        $rules = [
            'user_id' => 'required',
            'type' => 'required|string',
        ];

        // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم نقطه',
            'user_id.required' => 'يجب ادخال الموظف المراد تحويله لمفتش',
            'position.required' => 'يجب ادخال الرتبه',
            'Id_number.required' => 'يجب ادخال رقم الهويه ',

            'type.required' => 'يجب اختيار نوع المفتش',

        ];

        // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $inspector = Inspector::find($id);
        $user = User::findOrFail($request->user_id);
        $inspector->name = $user->name;
        $inspector->phone = $user->phone;
        //Buildings => مفتش مبانى  , internbilding =>مفتش متدرب مبانى   , internslok=> مفتس متدرب سلوك انضباطى   ,slok=>  مفتش سلوك
        $inspector->type = $request->type;

        $inspector->position = $user->position;

        $inspector->user_id = $request->user_id;
        $inspector->Id_number = $user->Civil_number;
        // $inspector->department_id = $user->department_id;
        $inspector->save();
        // $inspector->save();
        // dd($inspector->id);
        return redirect()->route('inspectors.index')
            ->with('success', 'Inspector updated successfully.')->with('showModal', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
