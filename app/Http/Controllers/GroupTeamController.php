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
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\instantmission;

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
    // public function update(Request $request, $id)
    // {
    //     // Custom validation messages
    //     $messages = [
    //         'name.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
    //         'working_tree_id.required' => 'نظام العمل مطلوبة ولا يمكن تركها فارغة.',


    //     ];

    //     // Validate the request input
    //     $request->validate([
    //         'name' => 'required|string',
    //         'working_tree_id' => 'required',


    //     ], $messages);

    //     $team = GroupTeam::find($id);

    //     if (!$team) {
    //         return redirect()->back()->withErrors(['team_not_found' => 'فريق العمل غير موجود.']);
    //     }


    //     $newName = $request->name;

    //     $newInspectors = $request->inspectors_ids ? (array) $request->inspectors_ids : [];
    //     $oldInspectorIds = $team->inspector_ids ? explode(',', $team->inspector_ids) : [];

    //     foreach ($newInspectors as $key) {
    //         $value =   Inspector::find($key);
    //         $value->group_id = $team->group_id;
    //         $value->save();
    //     }
    //     // Ensure all IDs are strings for comparison
    //     $newInspectors = array_map('strval', $newInspectors);
    //     $oldInspectorIds = array_map('strval', $oldInspectorIds);

    //     $changeArr = array_diff($newInspectors, $oldInspectorIds);

    //     // Also check for removed inspector IDs
    //     $removedArr = array_diff($oldInspectorIds, $newInspectors);

    //     if (empty($changeArr) && empty($removedArr) && $team->name === $newName && $team->working_tree_id == $request->working_tree_id) {
    //         return redirect()->back()->withErrors(['nothing_updated' => 'لم يتم تحديث أي بيانات.']);
    //     }

    //     // Update the team name
    //     $team->name = $newName;
    //     $team->working_tree_id = $request->working_tree_id;

    //     // Update inspector_ids if provided
    //     if (!empty($newInspectors)) {
    //         $team->inspector_ids = implode(",", $newInspectors);
    //     } else {
    //         // If no inspectors are provided, clear the inspector_ids
    //         $team->inspector_ids = '';
    //     }

    //     // Save the changes
    //     $team->save();

    //     return redirect()->route('groupTeam.index', $team->group_id)->with('success', 'تم التعديل بنجاح');
    // }

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

        // Update inspector information
        foreach ($newInspectors as $key) {
            $value = Inspector::find($key);
            $value->group_id = $team->group_id;
            $value->save();
        }

        // Ensure all IDs are strings for comparison
        $newInspectors = array_map('strval', $newInspectors);
        $oldInspectorIds = array_map('strval', $oldInspectorIds);

        $changeArr = array_diff($newInspectors, $oldInspectorIds);
        $removedArr = array_diff($oldInspectorIds, $newInspectors);

        // dd($changeArr, $removedArr);
        if (empty($changeArr) && empty($removedArr) && $team->name === $newName && $team->working_tree_id == $request->working_tree_id) {
            return redirect()->back()->withErrors(['nothing_updated' => 'لم يتم تحديث أي بيانات.']);
        }

        // Update the team name and working tree
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

        // Generate or update InspectorMission records
        $start_day_date = date('Y-m-d');
        $currentDate = Carbon::now();

        // Determine the total number of days in the current month
        $totalDaysInMonth = $currentDate->endOfMonth()->day;

        // Calculate the number of days left in the month
        $num_days = $totalDaysInMonth -  now()->day;
        foreach ($removedArr as $Inspector) {
            InspectorMission::where('inspector_id', $Inspector)->where('date', '>=', today())->delete();
        }
        foreach ($changeArr as $Inspector) {
            $date = $start_day_date; // Start from the 1st of the month
            $GroupTeam = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$Inspector])->first();

            if ($GroupTeam) {
                $WorkingTree = WorkingTree::find($GroupTeam->working_tree_id);
                if (!$WorkingTree || !$GroupTeam) {
                    Log::warning("Inspector ID $Inspector does not have a valid working tree or group team.");
                    continue;
                }

                $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;

                for ($day_of_month = 1; $day_of_month <= $num_days + 1; $day_of_month++) {
                    $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
                    $is_day_off = $day_in_cycle > $WorkingTree->working_days_num;
                    $WorkingTreeTime = !$is_day_off
                        ? WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                        ->where('day_num', $day_in_cycle)
                        ->first()
                        : null;

                    $inspectorMission = new InspectorMission();
                    $inspectorMission->inspector_id = $Inspector;
                    $inspectorMission->group_id = $GroupTeam->group_id;
                    $inspectorMission->group_team_id = $GroupTeam->id;
                    $inspectorMission->working_tree_id = $GroupTeam->working_tree_id;
                    $inspectorMission->working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;
                    $inspectorMission->date = $date;
                    $inspectorMission->day_off = $is_day_off ? 1 : 0;
                    $inspectorMission->save();

                    $date = date('Y-m-d', strtotime($date . ' +1 day'));
                }
            }
        }

        return redirect()->route('groupTeam.index', $team->group_id)->with('success', 'تم التعديل بنجاح');
    }

    public function updateTransfer(Request $request, $group_id)
    {
        // Get the inspectors' IDs and team IDs from the request
        $inspectorIds = $request->inspectors_ids;
        $teams = $request->team_id;

        // Prepare an array to keep track of transferred inspectors
        $transferredInspectors = [];

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
                    // Mark this inspector as transferred
                    $transferredInspectors[] = $inspectorId;

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

        // Only update inspector missions for transferred inspectors
        foreach ($transferredInspectors as $inspectorId) {
            $inspector_missions = InspectorMission::where('inspector_id', $inspectorId)->where('date', '>=', today())->get();
            $currentGroup = GroupTeam::where('group_id', $group_id)->where('id', $teams[$inspectorId])->first();
            $WorkingTree = WorkingTree::find($currentGroup->working_tree_id);
            $start_day_date = date('Y-m-d');
            // $num_days = date('t', strtotime($start_day_date));
            $currentDate = Carbon::now();

            // Determine the total number of days in the current month
            $totalDaysInMonth = $currentDate->endOfMonth()->day;

            // Calculate the number of days left in the month
            $num_days = $totalDaysInMonth -  now()->day;
            $day_of_month = 1;
            if (count($inspector_missions)) {

                $date = $start_day_date; // Start from the 1st of the month
                foreach ($inspector_missions as $inspector_mission) {
                    $total_days_in_cycle = $WorkingTree->working_days_num + $WorkingTree->holiday_days_num;
                    // Loop by number of days in the month
                    // for ($day_of_month = 1; $day_of_month <= $num_days; $day_of_month++) {
                    // Check day off or not
                    $day_in_cycle = ($day_of_month - 1) % $total_days_in_cycle + 1;
                    $is_day_off = $day_in_cycle > $WorkingTree->working_days_num;
                    // Get working tree time if not a day off
                    $WorkingTreeTime = !$is_day_off
                        ? WorkingTreeTime::where('working_tree_id', $WorkingTree->id)
                        ->where('day_num', $day_in_cycle)
                        ->first()
                        : null;
                    // Insert data for monthly
                    $inspector_mission->inspector_id = $inspectorId;
                    $inspector_mission->group_id = $currentGroup->group_id;
                    $inspector_mission->group_team_id = $currentGroup->id;
                    $inspector_mission->working_tree_id = $currentGroup->working_tree_id;
                    $inspector_mission->working_time_id = $WorkingTreeTime ? $WorkingTreeTime->working_time_id : null;
                    $inspector_mission->date = $date;
                    $inspector_mission->day_off = $is_day_off ? 1 : 0;
                    $inspector_mission->save();

                    // Move to the next day
                    $date = date('Y-m-d', strtotime($date . ' +1 day'));
                    $day_of_month++;
                    // }
                }
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
    public function IspectorMession()
    {
        // $data = [];
        $Groups = Groups::all();
        foreach ($Groups as $Group) {
            $currentDate = Carbon::now();

            $startOfMonth = $currentDate->copy()->startOfMonth();
            $endOfMonth = $currentDate->copy()->endOfMonth();

            $daysInMonth = [];
            $daysNum = [];
            $count = 1;
            Carbon::setLocale('ar');
            while ($startOfMonth->lte($endOfMonth)) {
                $daysInMonth[] = $startOfMonth->translatedFormat('l'); // 'l' gives the full name of the day in Arabic
                $daysNum[] = $count;
                $startOfMonth->addDay();
                $count++;
            }

            $Group['days_name'] = $daysInMonth;
            $Group['days_num'] = $daysNum;
            $GroupTeams = GroupTeam::where('group_id', $Group->id)->get();
            $Group['teams'] = $GroupTeams;
            foreach ($Group['teams'] as $GroupTeam) {
                $inspector_ids = $GroupTeam->inspector_ids;
                $inspectorIds = explode(',', $inspector_ids);
                $inspectors = Inspector::whereIn('id', $inspectorIds)->get();
                $GroupTeam['inspectors'] = $inspectors;


                foreach ($GroupTeam['inspectors'] as $inspector) {
                    $inspector_missions = [];
                    $colors = [];
                    foreach ($Group['days_num'] as $num) {


                        $date = $currentDate->copy()->startOfMonth()->addDays($num - 1);
                        $inspector_mission = InspectorMission::where('date', $date->toDateString())->where('inspector_id', $inspector->id)->where('group_id', $Group->id)->where('group_team_id', $GroupTeam->id)->first();
                        if ($inspector_mission) {
                            if ($inspector_mission->ids_group_point) {
                                $points = $inspector_mission->ids_group_point;
                                $GroupPoints = Grouppoint::whereIn('id', $points)->get();
                            } else {
                                $GroupPoints = [];
                            }
                            $inspector_mission['points'] = $GroupPoints;
                            if ($inspector_mission->ids_instant_mission) {

                                $missions = $inspector_mission->ids_instant_mission;
                                $InstantMissions = instantmission::whereIn('id', $missions)->get();
                            } else {
                                $InstantMissions = [];
                            }
                            $inspector_mission['instant_missions'] = $InstantMissions;
                        } else {
                            $inspector_mission = null;
                        }
                        $inspector_missions[] = $inspector_mission;

                        $inspector_mission_check = InspectorMission::where('date', $date->toDateString())->where('group_id', $Group->id)->where('group_team_id', $GroupTeam->id)->first();
// dd($inspector_mission_check);
                        if ($inspector_mission_check) {
                            $WorkingTreeTime = WorkingTime::find($inspector_mission_check->working_time_id);
                            if ($WorkingTreeTime) {
                                $colors[] = $WorkingTreeTime->color;
                            } else {
                                $colors[] = '#b9b5b4';
                            }
                        }
                    }
                    $GroupTeam['colors'] = $colors;
                    // dd($colors);
                    $inspector['missions'] = $inspector_missions;
                }
            }
        }
        // dd($Groups);

        return view('inspectorMission.index', compact('Groups'));
    }
}
