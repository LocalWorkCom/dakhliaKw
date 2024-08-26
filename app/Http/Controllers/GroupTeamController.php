<?php

namespace App\Http\Controllers;

use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\WorkingTime;
use App\Models\WorkingTree;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\WorkingTreeTime;
use App\Models\InspectorMission;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Console\Commands\inspector_mission;
use App\Http\Requests\StoreGroupTeamRequest;
use App\Http\Requests\UpdateGroupTeamRequest;
use App\Models\EmployeeVacation;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\instantmission;
use App\Models\PersonalMission;
use Illuminate\Support\Facades\DB;

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
                    $departmentId = auth()->user()->department_id; // Or however you determine the department ID

                    // Count the number of inspectors
                    if (auth()->user()->rule_id == 2) {
                        $count = count($inspectorIds);
                    } else {

                        $count = Inspector::leftJoin('users', 'inspectors.user_id', '=', 'users.id')
                            ->where('users.department_id', $departmentId)
                            ->where('users.id', '<>', auth()->user()->id)->whereIn('inspectors.id', $inspectorIds)->count();
                    }
                } else {
                    $count = 0;
                }
                if ($count == 1 || $count == 0) {

                    $btn = '<a class="btn btn-sm"   style="background-color: #F7AF15;     padding-inline: 15px;
" href=' . route('groupTeam.edit', $row->id) . '>' . $count . '</a>';
                } else {
                    $btn = '<a class="btn btn-sm"   style="background-color: #274373; padding-inline: 15px;" href=' . route('groupTeam.edit', $row->id) . '>' . $count . '</a>';
                }
                // dd($btn);
                return  $btn;
            })
            // ->addColumn('service_order', function ($row) {
            //     if ($row->service_order) {
            //         return  'نعم';
            //     } else {
            //         return  'لا';
            //     }
            // })
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
            $grouptemItem->service_order = ($request->service_order) ? $request->service_order : 0;
            $grouptemItem->save();

            return redirect()->back()->with('success', 'تم الاضافة بنجاح');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
    /**
     * team a newly created resource in storage.
     */
    // public function team($id)
    // {
    //     $arrayInspector = [];
    //     $inspector = Inspector::where('group_id', $id)->get();
    //     foreach ($inspector as $item) {
    //         $check = GroupTeam::where('group_id', $id)
    //             ->whereRaw('find_in_set(?, inspector_ids)', [$item->id])
    //             ->exists();
    //         if (!$check) {
    //             $arrayInspector[] = $item;
    //         }
    //     }

    //     // At this point, $arrayInspector contains the inspectors that do not exist in the inspector_ids column of GroupTeam
    //     $data = $arrayInspector;


    //     // dd($group);
    //     if ($inspector) {
    //         return response()->json(['success' => true, 'data' => $data]);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Record not found'], 404);
    //     }
    // }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $team = GroupTeam::find($id);
        $group_id = $team->group_id;
        $workTrees  = WorkingTree::all();
        $departmentId = auth()->user()->department_id; // Or however you determine the department ID
        if (auth()->user()->rule_id == 2) {
            $inspectors = Inspector::leftJoin('users', 'inspectors.user_id', '=', 'users.id')
                ->where('inspectors.flag', 0)
                ->where(function ($query) use ($team) {
                    $query->where('inspectors.group_id', $team->group_id)
                        ->orWhereNull('inspectors.group_id');
                })
                ->select("inspectors.*")->get();
        } else {
            $inspectors = Inspector::leftJoin('users', 'inspectors.user_id', '=', 'users.id')
                ->where('inspectors.flag', 0)
                ->where('users.department_id', $departmentId)
                ->where(function ($query) use ($team) {
                    $query->where('inspectors.group_id', $team->group_id)
                        ->orWhereNull('inspectors.group_id');
                })
                ->where('users.id', '<>', auth()->user()->id)
                ->select("inspectors.*")->get();
        }

        // $inspectors = Inspector::where('group_id', $team->group_id)->orwhereNull('group_id')->get();
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
        $inspectors = Inspector::whereIn('id', $inspectorIds)
            ->where('inspectors.flag', 0)->get();
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

        // Validate the request input with custom messages
        $request->validate([
            'name' => 'required|string',
            'working_tree_id' => 'required',
        ], $messages);

        // Find the GroupTeam by ID
        $team = GroupTeam::find($id);

        // Check if the team exists
        if (!$team) {
            // Redirect back with an error if the team is not found
            return redirect()->back()->withErrors(['team_not_found' => 'دورية العمل غير موجود.']);
        }

        // Retrieve the new name and inspector IDs from the request
        $newName = $request->name;
        $service_order = $request->service_order;
        $inspector_manager = $request->inspector_manager;

        $newInspectors = $request->inspectors_ids ? (array) $request->inspectors_ids : [];
        $oldInspectorIds = $team->inspector_ids ? explode(',', $team->inspector_ids) : [];
        // dd($newInspectors, $oldInspectorIds);
        if ($inspector_manager != $team->inspector_manager) {

            if (!in_array($inspector_manager, $newInspectors)) {
                $newInspectors[] = $inspector_manager;
            }
        }
        // Update group ID for each inspector in the new inspectors list
        foreach ($newInspectors as $key) {
            $value = Inspector::find($key);
            $value->group_id = $team->group_id;
            $value->save();
        }

        // Ensure all IDs are strings for comparison purposes
        $newInspectors = array_map('strval', $newInspectors);
        $oldInspectorIds = array_map('strval', $oldInspectorIds);
        $points = [];

        // Determine which inspectors were added or removed
        $changeArr = array_diff($newInspectors, $oldInspectorIds);
        $removedArr = array_diff($oldInspectorIds, $newInspectors);
        // Check if there are any changes; if not, return with a message
        if (empty($changeArr) && empty($removedArr) && $team->name === $newName && $team->working_tree_id == $request->working_tree_id && $service_order == $team->service_order &&  $inspector_manager == $team->inspector_manager) {
            return redirect()->back()->withErrors(['nothing_updated' => 'لم يتم تحديث أي بيانات.']);
        }

        // Update the team name and working tree ID
        if ($team->working_tree_id != $request->working_tree_id) {
            $data_mission = InspectorMission::where('date', '>=', today())->where('working_tree_id', $team->working_tree_id)->get();
            $old_work_tree = WorkingTree::find($team->working_tree_id);
            $new_work_tree = WorkingTree::find($request->working_tree_id);
            $old_total_days = $old_work_tree->working_days_num + $old_work_tree->holiday_days_num;
            $new_total_days = $new_work_tree->working_days_num + $new_work_tree->holiday_days_num;
            $count = 0;
            foreach ($data_mission as $data) {
                if ($data->day_number > $old_total_days) {
                    $count++;
                } else {
                    $count = $data->day_number;
                }
                if ($count == $new_total_days) {
                    $count = 1;
                }
                $work_tree_time = WorkingTreeTime::where('working_tree_id', $request->working_tree_id)->where('day_num', $count)->first();

                $data->working_tree_id = $request->working_tree_id;
                $data->working_time_id = $work_tree_time->working_time_id;
                $data->day_off = ($work_tree_time->working_time_id) ? 0 : 1;
                $data->save();
            }
        }

        $team->name = $newName;
        if (in_array($inspector_manager, $removedArr)) {
            $team->inspector_manager = null;
        } else {

            $team->inspector_manager = $inspector_manager;
        }
        $team->working_tree_id = $request->working_tree_id;
        $team->service_order = ($service_order) ? $service_order : 0;

        // Update inspector_ids if provided; otherwise, clear the field
        if (!empty($newInspectors)) {
            $team->inspector_ids = implode(",", $newInspectors);
        } else {
            $team->inspector_ids = '';
        }

        // Save the changes to the team
        $team->save();

        // Prepare to generate or update InspectorMission records
        $start_day_date = date('Y-m-d');
        $currentDate = Carbon::now();

        // Determine the total number of days in the current month
        $totalDaysInMonth = $currentDate->endOfMonth()->day;

        // Calculate the number of days remaining in the month
        $num_days = $totalDaysInMonth - now()->day;

        // Delete missions for removed inspectors
        foreach ($removedArr as $Inspector) {
            InspectorMission::where('inspector_id', $Inspector)->where('date', '>=', today())->delete();
        }

        // Generate missions for added or changed inspectors
        foreach ($changeArr as $Inspector) {
            $vacation_days = 0;
            $date = $start_day_date; // Start from the current date
            $GroupTeam = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$Inspector])->first();

            // Check if the group team and working tree are valid
            if ($GroupTeam) {
                $WorkingTree = WorkingTree::find($GroupTeam->working_tree_id);
                if (!$WorkingTree || !$GroupTeam) {
                    Log::warning("Inspector ID $Inspector does not have a valid working tree or group team.");
                    continue;
                }

                // Calculate the total days in the working cycle
                $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;

                // Loop through the remaining days of the month
                for ($day_of_month = 1; $day_of_month <= $num_days + 1; $day_of_month++) {
                    // Determine if the day is a working day or a day off
                    $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
                    $is_day_off = $day_in_cycle > $WorkingTree->working_days_num;
                    $WorkingTreeTime = !$is_day_off
                        ? WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                        ->where('day_num', $day_in_cycle)
                        ->first()
                        : null;

                    // Create a new InspectorMission record
                    $getExistPoints = InspectorMission::where('group_team_id', $GroupTeam->id)->where('group_id', $GroupTeam->group_id)
                        ->where('working_tree_id', $GroupTeam->working_tree_id)
                        ->where('date', $date)->first();
                    // dd($getExistPoints);
                    if ($getExistPoints) {
                        $points = $getExistPoints->ids_group_point;
                        $day_off = $getExistPoints->day_off;
                        $working_time_id = $getExistPoints->working_time_id;
                    } else {
                        $day_off = $is_day_off ? 1 : 0;
                        $working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;
                    }

                    $user_id  = Inspector::find($Inspector)->user_id;

                    if ($vacation_days == 0) {

                        $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('start_date', '=',  $date)->first(); //1/9/2024
                        if ($EmployeeVacation) {
                            $vacation_days = $EmployeeVacation->days_number; //3
                        }
                    }
                    $inspectorMission = new InspectorMission();
                    $inspectorMission->inspector_id = $Inspector;
                    $inspectorMission->group_id = $GroupTeam->group_id;
                    $inspectorMission->group_team_id = $GroupTeam->id;
                    $inspectorMission->working_tree_id = $GroupTeam->working_tree_id;
                    $inspectorMission->working_time_id = $working_time_id;
                    $inspectorMission->date = $date;
                    $inspectorMission->ids_group_point = $points;
                    $inspectorMission->day_off = $day_off;
                    $inspectorMission->day_number = $day_in_cycle;
                    if ($vacation_days != 0) {
                        $inspectorMission->vacation_id = $EmployeeVacation->id;
                    }
                    $inspectorMission->save();
                    if ($vacation_days != 0) {
                        $vacation_days--;
                    }
                    // Move to the next day
                    $date = date('Y-m-d', strtotime($date . ' +1 day'));
                }
            }
        }

        // Redirect back to the group team index with a success message
        return redirect()->route('groupTeam.index', $team->group_id)->with('success', 'تم التعديل بنجاح');
    }


    /**
     * updateTransfer the inspector from team to team.
     */
    // public function updateTransfer(Request $request, $group_id)
    // {
    //     // Get the inspectors' IDs and team IDs from the request
    //     $inspectorIds = $request->inspectors_ids;
    //     $teams = $request->team_id;

    //     // Prepare an array to keep track of transferred inspectors
    //     $transferredInspectors = [];
    //     $points = [];

    //     // Iterate over each inspector ID
    //     foreach ($inspectorIds as $inspectorId) {
    //         // Get the new team ID for the current inspector
    //         $newTeamId = $teams[$inspectorId];
    //         // Get the current group information based on group ID and team ID
    //         $currentGroup = GroupTeam::where('group_id', $group_id)->where('id', $newTeamId)->first();

    //         // Check if the inspector is already in a different group
    //         $existingGroupQuery = GroupTeam::where('group_id', $group_id)
    //             ->whereRaw('find_in_set(?, inspector_ids)', [$inspectorId]);

    //         // If the inspector is found in another group
    //         if ($existingGroupQuery->exists()) {
    //             $existingGroup = $existingGroupQuery->first();

    //             // If the inspector is not already in the new team, transfer them
    //             if ($existingGroup->id != $newTeamId) {
    //                 // Mark this inspector as transferred
    //                 $transferredInspectors[] = $inspectorId;

    //                 // Remove inspector from the old group
    //                 $oldInspectorIds = explode(',', $existingGroup->inspector_ids);
    //                 $updatedOldInspectorIds = array_diff($oldInspectorIds, [$inspectorId]);
    //                 $existingGroup->inspector_ids = implode(',', $updatedOldInspectorIds);

    //                 $existingGroup->save();

    //                 //check if exist in team manager 
    //                 if ($existingGroup->inspector_manager == $inspectorId) {
    //                     $existingGroup->inspector_manager = null;
    //                     $existingGroup->save();
    //                 }

    //                 // Add inspector to the new group
    //                 if (empty($currentGroup->inspector_ids)) {
    //                     // If the new group has no inspectors, simply add the inspector
    //                     $currentGroup->inspector_ids = $inspectorId;
    //                 } else {
    //                     // Otherwise, append the inspector to the existing list
    //                     $currentInspectorIds = explode(',', $currentGroup->inspector_ids);
    //                     $currentInspectorIds[] = $inspectorId;
    //                     $currentGroup->inspector_ids = implode(',', $currentInspectorIds);
    //                 }
    //                 $currentGroup->save();
    //             }
    //         } else {
    //             // If the inspector is not already assigned to a group
    //             if (empty($currentGroup->inspector_ids)) {
    //                 // If the new group has no inspectors, add the inspector
    //                 $currentGroup->inspector_ids = $inspectorId;
    //                 $transferredInspectors[] = $inspectorId;
    //             } else {
    //                 // Otherwise, append the inspector to the existing list
    //                 $currentInspectorIds = explode(',', $currentGroup->inspector_ids);
    //                 $currentInspectorIds[] = $inspectorId;
    //                 $currentGroup->inspector_ids = implode(',', $currentInspectorIds);
    //             }
    //             $currentGroup->save();
    //         }
    //     }
    //     // $changeArr = array_diff($inspectorIds, $oldInspectorIds);
    //     // Only update inspector missions for transferred inspectors
    //     foreach ($transferredInspectors as $inspectorId) {
    //         dd(1);

    //         $vacation_days = 0;

    //         // Get current missions for the inspector starting from today
    //         $inspector_missions = InspectorMission::where('inspector_id', $inspectorId)->where('date', '>=', today())->get();
    //         // Get the current group and its working tree
    //         $currentGroup = GroupTeam::where('group_id', $group_id)->where('id', $teams[$inspectorId])->first();
    //         $WorkingTree = WorkingTree::find($currentGroup->working_tree_id);
    //         $start_day_date = date('Y-m-d'); // Start from today's date
    //         $currentDate = Carbon::now();

    //         // Determine the total number of days in the current month
    //         $totalDaysInMonth = $currentDate->endOfMonth()->day;

    //         // Calculate the number of days left in the month
    //         $num_days = $totalDaysInMonth - now()->day;
    //         $day_of_month = 1;

    //         // If there are existing missions for the inspector
    //         if (count($inspector_missions)) {
    //             $date = $start_day_date; // Start from today's date
    //             foreach ($inspector_missions as $inspector_mission) {
    //                 // Calculate the cycle of work and holidays
    //                 $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;
    //                 // Calculate which day in the cycle this is
    //                 $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
    //                 // Determine if the day is a day off
    //                 $is_day_off = $day_in_cycle > $WorkingTree->working_days_num;
    //                 // Get the working tree time if it's not a day off
    //                 $WorkingTreeTime = !$is_day_off
    //                     ? WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
    //                     ->where('day_num', $day_in_cycle)
    //                     ->first()
    //                     : null;

    //                 $getExistPoints = InspectorMission::where('group_team_id', $currentGroup->id)->where('group_id', $currentGroup->group_id)
    //                     ->where('working_tree_id', $currentGroup->working_tree_id)
    //                     ->where('date', $date)->first();
    //                 // dd($getExistPoints);
    //                 if ($getExistPoints) {
    //                     $points = $getExistPoints->ids_group_point;
    //                     $day_off = $getExistPoints->day_off;
    //                     $working_time_id = $getExistPoints->working_time_id;
    //                 } else {
    //                     $day_off = $is_day_off ? 1 : 0;
    //                     $working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;
    //                 }
    //                 $user_id  = Inspector::find($inspectorId)->user_id;

    //                 if ($vacation_days == 0) {

    //                     $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('start_date', '=',  $date)->first(); //1/9/2024
    //                     if ($EmployeeVacation) {
    //                         $vacation_days = $EmployeeVacation->days_number; //3
    //                     }
    //                 }
    //                 // $getCurrentPoints = InspectorMission::where('group_id', $currentGroup->group_id)->where('group_team_id', $currentGroup->id)->where('date', $date)->first()->ids_group_point;
    //                 // Update the inspector's mission details
    //                 $inspector_mission->inspector_id = $inspectorId;
    //                 $inspector_mission->group_id = $currentGroup->group_id;
    //                 $inspector_mission->group_team_id = $currentGroup->id;
    //                 $inspector_mission->working_tree_id = $currentGroup->working_tree_id;
    //                 $inspector_mission->working_time_id = $working_time_id;
    //                 $inspector_mission->date = $date;
    //                 $inspector_mission->ids_group_point = $points;
    //                 $inspector_mission->day_off = $day_off;
    //                 if ($vacation_days != 0) {
    //                     $inspector_mission->vacation_id = $EmployeeVacation->id;
    //                 }
    //                 $inspector_mission->save();
    //                 if ($vacation_days != 0) {
    //                     $vacation_days--;
    //                 }
    //                 // Move to the next day
    //                 $date = date('Y-m-d', strtotime($date . ' +1 day'));
    //                 $day_of_month++;
    //             }
    //         } else {
    //             // If there are no existing missions, create new ones
    //             $date = $start_day_date; // Start from today's date
    //             $GroupTeam = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->first();

    //             if ($GroupTeam) {
    //                 // Validate working tree and group team existence
    //                 $WorkingTree = WorkingTree::find($GroupTeam->working_tree_id);
    //                 if (!$WorkingTree || !$GroupTeam) {
    //                     Log::warning("Inspector ID $inspectorId does not have a valid working tree or group team.");
    //                     continue;
    //                 }

    //                 // Calculate the total days in the working cycle
    //                 $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;

    //                 // Loop through the remaining days of the month
    //                 for ($day_of_month = 1; $day_of_month <= $num_days + 1; $day_of_month++) {
    //                     // Calculate which day in the cycle this is
    //                     $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
    //                     // Determine if the day is a day off
    //                     $is_day_off = $day_in_cycle > $WorkingTree->working_days_num;
    //                     // Get the working tree time if it's not a day off
    //                     $WorkingTreeTime = !$is_day_off
    //                         ? WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
    //                         ->where('day_num', $day_in_cycle)
    //                         ->first()
    //                         : null;

    //                     $getExistPoints = InspectorMission::where('group_team_id', $GroupTeam->id)->where('group_id', $GroupTeam->group_id)
    //                         ->where('working_tree_id', $GroupTeam->working_tree_id)->where('working_time_id', $WorkingTreeTime->working_time_id)
    //                         ->where('date', $date)->first();
    //                     if ($getExistPoints) {
    //                         $points = $getExistPoints->ids_group_point;
    //                         $day_off = $getExistPoints->day_off;
    //                         $working_time_id = $getExistPoints->working_time_id;
    //                     } else {
    //                         $day_off = $is_day_off ? 1 : 0;
    //                         $working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;
    //                     }
    //                     $user_id  = Inspector::find($inspectorId)->user_id;

    //                     if ($vacation_days == 0) {

    //                         $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('start_date', '=',  $date)->first(); //1/9/2024
    //                         if ($EmployeeVacation) {
    //                             $vacation_days = $EmployeeVacation->days_number; //3
    //                         }
    //                     }
    //                     // Create a new inspector mission
    //                     $inspectorMission = new InspectorMission();
    //                     $inspectorMission->inspector_id = $inspectorId;
    //                     $inspectorMission->group_id = $GroupTeam->group_id;
    //                     $inspectorMission->group_team_id = $GroupTeam->id;
    //                     $inspectorMission->working_tree_id = $GroupTeam->working_tree_id;
    //                     $inspectorMission->working_time_id = $working_time_id;
    //                     $inspectorMission->date = $date;
    //                     if ($vacation_days != 0) {
    //                         $inspectorMission->vacation_id = $EmployeeVacation->id;
    //                     }
    //                     $inspectorMission->ids_group_point = $points;
    //                     $inspectorMission->day_off = $day_off;
    //                     $inspectorMission->save();
    //                     if ($vacation_days != 0) {
    //                         $vacation_days--;
    //                     }
    //                     // Move to the next day
    //                     $date = date('Y-m-d', strtotime($date . ' +1 day'));
    //                 }
    //             }
    //         }
    //     }

    //     // Redirect back to the group team index with a success message
    //     return redirect()->route('groupTeam.index', $group_id)->with('success', 'تم التعديل بنجاح');
    // }
    public function updateTransfer(Request $request, $group_id)
    {
        // Get the inspectors' IDs and team IDs from the request
        $inspectorIds = $request->inspectors_ids;
        $teams = $request->team_id;

        // Prepare arrays to keep track of transferred and removed inspectors
        $transferredInspectors = [];
        $removedInspectors = [];

        // Retrieve all current GroupTeam records for the given group
        $currentGroups = GroupTeam::where('group_id', $group_id)->get();

        // First, handle transfers and track removed inspectors
        foreach ($currentGroups as $currentGroup) {
            $currentInspectorIds = explode(',', $currentGroup->inspector_ids);

            // Identify inspectors that are no longer in the new list
            $inspectorsToRemove = array_diff($currentInspectorIds, $inspectorIds);

            // Remove these inspectors from the current group
            foreach ($inspectorsToRemove as $inspectorId) {
                $removedInspectors[] = $inspectorId;
                $currentGroup->inspector_ids = implode(',', array_diff($currentInspectorIds, [$inspectorId]));
                $currentGroup->save();

                // If the inspector was a manager, clear the manager field
                if ($currentGroup->inspector_manager == $inspectorId) {
                    $currentGroup->inspector_manager = null;
                    $currentGroup->save();
                }
            }

            // Handle transfers for inspectors that are in the request
            foreach ($inspectorIds as $inspectorId) {
                $newTeamId = $teams[$inspectorId];
                $currentGroup = GroupTeam::where('group_id', $group_id)->where('id', $newTeamId)->first();

                $existingGroupQuery = GroupTeam::where('group_id', $group_id)
                    ->whereRaw('find_in_set(?, inspector_ids)', [$inspectorId]);

                if ($existingGroupQuery->exists()) {
                    $existingGroup = $existingGroupQuery->first();

                    if ($existingGroup->id != $newTeamId) {
                        $transferredInspectors[] = $inspectorId;

                        // Remove inspector from the old group
                        $oldInspectorIds = explode(',', $existingGroup->inspector_ids);
                        $updatedOldInspectorIds = array_diff($oldInspectorIds, [$inspectorId]);
                        $existingGroup->inspector_ids = implode(',', $updatedOldInspectorIds);

                        $existingGroup->save();

                        if ($existingGroup->inspector_manager == $inspectorId) {
                            $existingGroup->inspector_manager = null;
                            $existingGroup->save();
                        }

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
                    if (empty($currentGroup->inspector_ids)) {
                        $currentGroup->inspector_ids = $inspectorId;
                        $transferredInspectors[] = $inspectorId;
                    } else {
                        $currentInspectorIds = explode(',', $currentGroup->inspector_ids);
                        $currentInspectorIds[] = $inspectorId;
                        $currentGroup->inspector_ids = implode(',', $currentInspectorIds);
                    }
                    $currentGroup->save();
                }
            }
        }

        // Handle the missions update for transferred inspectors
        foreach ($transferredInspectors as $inspectorId) {

            $vacation_days = 0;

            // Get current missions for the inspector starting from today
            $inspector_missions = InspectorMission::where('inspector_id', $inspectorId)->where('date', '>=', today())->get();
            // Get the current group and its working tree
            $currentGroup = GroupTeam::where('group_id', $group_id)->where('id', $teams[$inspectorId])->first();
            $WorkingTree = WorkingTree::find($currentGroup->working_tree_id);
            $start_day_date = date('Y-m-d'); // Start from today's date
            $currentDate = Carbon::now();

            // Determine the total number of days in the current month
            $totalDaysInMonth = $currentDate->endOfMonth()->day;

            // Calculate the number of days left in the month
            $num_days = $totalDaysInMonth - now()->day;
            $day_of_month = 1;

            // If there are existing missions for the inspector
            if (count($inspector_missions)) {
                $date = $start_day_date; // Start from today's date
                foreach ($inspector_missions as $inspector_mission) {
                    // Calculate the cycle of work and holidays
                    $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;
                    // Calculate which day in the cycle this is
                    $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
                    // Determine if the day is a day off
                    $is_day_off = $day_in_cycle > $WorkingTree->working_days_num;
                    // Get the working tree time if it's not a day off
                    $WorkingTreeTime = !$is_day_off
                        ? WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                        ->where('day_num', $day_in_cycle)
                        ->first()
                        : null;

                    $getExistPoints = InspectorMission::where('group_team_id', $currentGroup->id)->where('group_id', $currentGroup->group_id)
                        ->where('working_tree_id', $currentGroup->working_tree_id)
                        ->where('date', $date)->first();
                    // dd($getExistPoints);
                    if ($getExistPoints) {
                        $points = $getExistPoints->ids_group_point;
                        $day_off = $getExistPoints->day_off;
                        $working_time_id = $getExistPoints->working_time_id;
                    } else {
                        $day_off = $is_day_off ? 1 : 0;
                        $working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;
                    }
                    $user_id  = Inspector::find($inspectorId)->user_id;

                    if ($vacation_days == 0) {

                        $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('start_date', '=',  $date)->first(); //1/9/2024
                        if ($EmployeeVacation) {
                            $vacation_days = $EmployeeVacation->days_number; //3
                        }
                    }
                    // $getCurrentPoints = InspectorMission::where('group_id', $currentGroup->group_id)->where('group_team_id', $currentGroup->id)->where('date', $date)->first()->ids_group_point;
                    // Update the inspector's mission details
                    $inspector_mission->inspector_id = $inspectorId;
                    $inspector_mission->group_id = $currentGroup->group_id;
                    $inspector_mission->group_team_id = $currentGroup->id;
                    $inspector_mission->working_tree_id = $currentGroup->working_tree_id;
                    $inspector_mission->working_time_id = $working_time_id;
                    $inspector_mission->date = $date;
                    $inspector_mission->ids_group_point = $points;
                    $inspector_mission->day_off = $day_off;
                    if ($vacation_days != 0) {
                        $inspector_mission->vacation_id = $EmployeeVacation->id;
                    }
                    $inspector_mission->save();
                    if ($vacation_days != 0) {
                        $vacation_days--;
                    }
                    // Move to the next day
                    $date = date('Y-m-d', strtotime($date . ' +1 day'));
                    $day_of_month++;
                }
            } else {
                // If there are no existing missions, create new ones
                $date = $start_day_date; // Start from today's date
                $GroupTeam = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->first();

                if ($GroupTeam) {
                    // Validate working tree and group team existence
                    $WorkingTree = WorkingTree::find($GroupTeam->working_tree_id);
                    if (!$WorkingTree || !$GroupTeam) {
                        Log::warning("Inspector ID $inspectorId does not have a valid working tree or group team.");
                        continue;
                    }

                    // Calculate the total days in the working cycle
                    $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;

                    // Loop through the remaining days of the month
                    for ($day_of_month = 1; $day_of_month <= $num_days + 1; $day_of_month++) {
                        // Calculate which day in the cycle this is
                        $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
                        // Determine if the day is a day off
                        $is_day_off = $day_in_cycle > $WorkingTree->working_days_num;
                        // Get the working tree time if it's not a day off
                        $WorkingTreeTime = !$is_day_off
                            ? WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                            ->where('day_num', $day_in_cycle)
                            ->first()
                            : null;

                        $getExistPoints = InspectorMission::where('group_team_id', $GroupTeam->id)->where('group_id', $GroupTeam->group_id)
                            ->where('working_tree_id', $GroupTeam->working_tree_id)->where('working_time_id', $WorkingTreeTime->working_time_id)
                            ->where('date', $date)->first();
                        if ($getExistPoints) {
                            $points = $getExistPoints->ids_group_point;
                            $day_off = $getExistPoints->day_off;
                            $working_time_id = $getExistPoints->working_time_id;
                        } else {
                            $day_off = $is_day_off ? 1 : 0;
                            $working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;
                        }
                        $user_id  = Inspector::find($inspectorId)->user_id;

                        if ($vacation_days == 0) {

                            $EmployeeVacation = EmployeeVacation::where('employee_id', $user_id)->where('start_date', '=',  $date)->first(); //1/9/2024
                            if ($EmployeeVacation) {
                                $vacation_days = $EmployeeVacation->days_number; //3
                            }
                        }
                        // Create a new inspector mission
                        $inspectorMission = new InspectorMission();
                        $inspectorMission->inspector_id = $inspectorId;
                        $inspectorMission->group_id = $GroupTeam->group_id;
                        $inspectorMission->group_team_id = $GroupTeam->id;
                        $inspectorMission->working_tree_id = $GroupTeam->working_tree_id;
                        $inspectorMission->working_time_id = $working_time_id;
                        $inspectorMission->date = $date;
                        $inspectorMission->day_number = $day_in_cycle;
                        if ($vacation_days != 0) {
                            $inspectorMission->vacation_id = $EmployeeVacation->id;
                        }
                        $inspectorMission->ids_group_point = $points;
                        $inspectorMission->day_off = $day_off;
                        $inspectorMission->save();
                        if ($vacation_days != 0) {
                            $vacation_days--;
                        }
                        // Move to the next day
                        $date = date('Y-m-d', strtotime($date . ' +1 day'));
                    }
                }
            }
        }

        // Handle the removal of inspectors from missions or other cleanup if necessary
        foreach ($removedInspectors as $inspectorId) {
            $inspector_missions = InspectorMission::where('inspector_id', $inspectorId)->where('date', '>=', today())->get();
            foreach ($inspector_missions as  $inspector_mission) {
                $inspector_mission->delete();
            }
        }

        // Redirect back to the group team index with a success message
        return redirect()->route('groupTeam.index', $group_id)->with('success', 'تم التعديل بنجاح');
    }


    /**
     * transfer the inspector from team to team.
     */

    public function transfer($group_id)
    {
        // Initialize an array to store the IDs of selected inspectors.
        $selectedInspectors = [];
        $departmentId = auth()->user()->department_id; // Or however you determine the department ID
        if (auth()->user()->rule_id == 2) {
            $inspectors = Inspector::leftJoin('users', 'inspectors.user_id', '=', 'users.id')
                ->where('inspectors.flag', 0)
                ->where('group_id', $group_id)
                ->select("inspectors.*")->get();
        } else {
            $inspectors = Inspector::leftJoin('users', 'inspectors.user_id', '=', 'users.id')
                ->where('users.department_id', $departmentId)
                ->where('inspectors.flag', 0)
                ->where('group_id', $group_id)
                ->where('users.id', '<>', auth()->user()->id)
                ->select("inspectors.*")->get();
        }

        // Fetch all inspectors belonging to the specified group along with their associated user data.
        // $inspectors = Inspector::with('user')->where('group_id', $group_id)->get();


        // Initialize a collection to hold the inspector groups and their associated group team IDs.
        $inspectorGroups = collect();

        // Loop through each inspector to find their associated group teams.
        foreach ($inspectors as $inspector) {
            // Get all group teams that the current inspector is a part of based on their ID.
            $groupTeams = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspector->id])->get();

            // Fetch all group teams belonging to the specified group.
            $GroupTeamGets = GroupTeam::where('group_id', $group_id)->get();

            // Loop through each group team to extract and store inspector IDs.
            foreach ($GroupTeamGets as $GroupTeamGet) {
                // Split the inspector_ids string into an array of IDs.
                $inspectorIds = explode(',', $GroupTeamGet->inspector_ids);

                // Merge the current inspector IDs with the selected inspectors array.
                $selectedInspectors = array_merge($selectedInspectors, $inspectorIds);
            }

            // Create an array mapping group team names to their IDs.
            $groupTeamIds = $groupTeams->pluck('id', 'name')->toArray();

            // Push the inspector data and associated group team IDs into the inspectorGroups collection.
            $inspectorGroups->push([
                'inspector_id' => $inspector,
                'group_team_ids' => $groupTeamIds
            ]);
        }

        // Fetch all group teams for the specified group.
        $allteams = GroupTeam::where('group_id', $group_id)->get();

        // Return the view with the necessary data to be used in the transfer interface.
        return view('groupteam.transfer', compact('inspectorGroups', 'allteams', 'group_id', 'selectedInspectors'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupTeam $groupTeam)
    {
        //
    }
    public function IspectorMession()
    {
        // Retrieve all Groups
        $Groups = Groups::all();
        // Retrieve all working times
        $working_times = WorkingTime::all();

        $inspectors = Inspector::with('user.grade')
            ->where('inspectors.flag', 0)->get();

        // foreach ($inspectors as $inspector) {
        //     if ($inspector->user && $inspector->user->grade) {
        //         return $inspector->user->grade->name;
        //     }
        // }

        foreach ($Groups as $Group) {
            // Initialize an array to hold teams associated with the group
            $group_teams = [];
            $currentDate = Carbon::now();

            // Get the start and end dates of the current month
            $startOfMonth = $currentDate->copy()->startOfMonth();
            $endOfMonth = $currentDate->copy()->endOfMonth();

            // Initialize arrays to hold the days of the month and their corresponding numbers
            $daysInMonth = [];
            $daysNum = [];
            $count = 1;
            Carbon::setLocale('ar'); // Set the locale to Arabic for day names

            // Loop through each day of the month
            while ($startOfMonth->lte($endOfMonth)) {
                // Get the full name of the day in Arabic
                $daysInMonth[] = $startOfMonth->translatedFormat('l');
                // Add the day's number to the array
                $daysNum[] = $count;
                $startOfMonth->addDay(); // Move to the next day
                $count++;
            }

            // Store the days' names and numbers in the Group object
            $Group['days_name'] = $daysInMonth;
            $Group['days_num'] = $daysNum;

            // Retrieve all teams associated with the current group
            $GroupTeams = GroupTeam::where('group_id', $Group->id)->get();
            foreach ($GroupTeams as $GroupTeam) {
                // Check if there are any inspector missions associated with the team and group
                $check = InspectorMission::where('group_id', $Group->id)
                    ->where('group_team_id', $GroupTeam->id)
                    ->first();
                if ($check) {
                    // If a mission exists, add the team to the group_teams array
                    $group_teams[] = $GroupTeam;
                }
            }
            // Assign the group_teams array to the Group object
            $Group['teams'] = $group_teams;
            // Process each team in the group
            foreach ($Group['teams'] as  $Team) {
                // dd(sizeof($Group['teams']));


                // Retrieve all inspectors associated with the team in the current group
                $inspectorIds = InspectorMission::where('group_id', $Group->id)
                    ->where('group_team_id', $Team->id)

                    ->groupBy('inspector_id')
                    ->pluck('inspector_id');
                // Get the inspector objects
                $inspectors = Inspector::whereIn('id', $inspectorIds->toArray())->get();
                $Team['inspectors'] = $inspectors;

                // Initialize an array to hold colors for each day in the month
                $colors = [];
                foreach ($Group['days_num'] as $num) {
                    // Calculate the date corresponding to the current day number
                    $date = $currentDate->copy()->startOfMonth()->addDays($num - 1);

                    // Check if there is a mission for the group, team, and date
                    $inspector_mission_check = InspectorMission::where('date', $date->toDateString())
                        ->where('group_id', $Group->id)
                        ->where('group_team_id', $Team->id)
                        ->first();

                    if ($inspector_mission_check) {
                        // Get the working time and its associated color
                        $WorkingTreeTime = WorkingTime::find($inspector_mission_check->working_time_id);
                        if ($WorkingTreeTime) {
                            $colors[] = $WorkingTreeTime->color;
                        } else {

                            $colors[] = 'white';
                        }
                    } else {
                        $colors[] = '#d6d6d6';
                    }
                }

                // Assign the colors array to the Team object
                $Team['colors'] = $colors;

                // Process each inspector in the team
                foreach ($Team['inspectors'] as $inspector) {

                    $colors = [];
                    $vacations = [];
                    $inspector_missions = [];
                    $pointsArray = [];
                    $instantArray = [];
                    $personalArray = [];
                    // Loop through each day of the month for the inspector
                    foreach ($Group['days_num'] as $index => $num) {
                        $date = $currentDate->copy()->startOfMonth()->addDays($num - 1);

                        // Retrieve the inspector's mission for the specific date, group, and team
                        $inspector_mission = InspectorMission::where('date', $date->toDateString())
                            ->where('inspector_id', $inspector->id)
                            ->where('group_id', $Group->id)
                            ->where('group_team_id', $Team->id)
                            ->first();

                        if ($inspector_mission) {

                            $today = date('Y-m-d');
                            $EmployeeVacation = EmployeeVacation::find($inspector_mission->vacation_id);

                            if ($EmployeeVacation) {
                                $days_number =   $EmployeeVacation->days_number;
                                if ($date->diffInDays($EmployeeVacation->start_date) < $days_number) {
                                    $vacations[] = $inspector_mission->vacation->vacation_type->name;
                                } else {

                                    if ($date->toDateString() <= $EmployeeVacation->end_date) {
                                        $vacations[] = 'متجاوزة';
                                    } else if ($EmployeeVacation->is_exceeded && $today >= $date->toDateString()) {
                                        $vacations[] = 'متجاوزة';
                                    } else {
                                        $vacations[] = null;
                                    }
                                }
                            } else {
                                $vacations[] =  null;
                            }
                            // Retrieve the group points associated with the mission
                            if ($inspector_mission->ids_group_point) {
                                $points = is_array($inspector_mission->ids_group_point)
                                    ? $inspector_mission->ids_group_point
                                    : explode(',', $inspector_mission->ids_group_point);
                                // dd($points);
                                $GroupPoints = Grouppoint::whereIn('id', $points)->get();
                            } else {
                                $GroupPoints = null;
                            }
                            $pointsArray[] = $GroupPoints;

                            // Retrieve the instant missions associated with the mission
                            if ($inspector_mission->ids_instant_mission) {
                                // $missions = $inspector_mission->ids_instant_mission;
                                $missions = is_array($inspector_mission->ids_instant_mission)
                                    ? $inspector_mission->ids_instant_mission
                                    : explode(',', $inspector_mission->ids_instant_mission);
                                $InstantMissions = instantmission::whereIn('id', $missions)->get();
                            } else {
                                $InstantMissions = null;
                            }

                            $instantArray[] = $InstantMissions;
                            // Retrieve the instant missions associated with the mission
                            if ($inspector_mission->personal_mission_ids) {
                                // $missions = $inspector_mission->personal_mission_ids;
                                $missions = is_array($inspector_mission->personal_mission_ids)
                                    ? $inspector_mission->personal_mission_ids
                                    : explode(',', $inspector_mission->personal_mission_ids);
                                $personalMissions = PersonalMission::whereIn('id', $missions)->get();
                            } else {
                                $personalMissions = null;
                            }

                            $personalArray[] = $personalMissions;

                            // Get the working time and its associated color
                            $WorkingTreeTime = WorkingTime::find($inspector_mission->working_time_id);
                            if ($WorkingTreeTime) {
                                $colors[] = $WorkingTreeTime->color;
                            } else {
                                // Default color if no working time is found
                                $colors[] = '#b9b5b4';
                            }
                        } else {
                            // Default color if no mission is found
                            $inspector_mission = null;
                            $colors[] = '#d6d6d6';
                            $vacations[] = null;
                            $pointsArray[] = null;
                            $instantArray[] = null;
                            $personalArray[] = null;
                        }

                        // Add the mission to the inspector's missions array
                        $inspector_missions[] = $inspector_mission;
                    }

                    $inspector['vacations'] = $vacations;

                    // Assign the missions and colors arrays to the inspector object
                    $inspector['missions'] = $inspector_missions;
                    $inspector['colors'] = $colors;
                    $inspector['points'] = $pointsArray;
                    $inspector['instant_missions'] = $instantArray;
                    $inspector['personal_missions'] = $personalArray;
                    // if ( $inspector->id == 10) {

                    //     dd([
                    //         'inspector_id' => $inspector->id,
                    //         'group_id' => $Group->id,
                    //         'group_team_id' => $Team->id,
                    //         'inspector_mission' => $inspector_missions,
                    //         'ids_group_point' => $inspector_mission ? $inspector_mission->ids_group_point : null,
                    //         'pointsArray' => $pointsArray,
                    //         'instantArray' => $instantArray, 
                    //         'inspector'=>$inspector
                    //     ]);
                    // }
                }
            }
        }
        // Remove groups that do not have any teams
        $Groups = $Groups->filter(function ($group) {
            return count($group['teams']) > 0;
        });
        // dd($Groups);
        // Return the view with the Groups data
        return view('inspectorMission.index', compact('Groups', 'working_times', 'inspectors'));
    }
}
