<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\outgoings;
use App\Models\Iotelegram;
use App\Models\departements;
use App\Models\EmployeeVacation;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\instantmission;
use App\Models\Point;
use App\Models\Statistic;
use App\Models\UserStatistic;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function test()
    {
        return view('test');
    }
    //
    public function index(Request $request)
    {
        $Statistics = Statistic::all();
        $counts = [];
        $routes = [];
        $UserStatistic = UserStatistic::where('user_id', Auth::user()->id)->where('checked', 1)->pluck('statistic_id');
        $statistics = Statistic::all();
        $GroupDatas = Groups::all();
        $GroupTeamDatas = GroupTeam::all();
        $PointDatas = Point::all();
        $GroupPointDatas = Grouppoint::all();
        $inspectorDatas = Inspector::all();
        if (count($UserStatistic) == 0) {
            foreach ($statistics as $statistic) {
                UserStatistic::create([
                    'user_id' => Auth::user()->id,
                    'statistic_id' => $statistic->id,
                    'checked' => true, // Set the `checked` column to true for all entries
                ]);
            }
            $UserStatistic = UserStatistic::where('user_id', Auth::user()->id)->where('checked', 1)->pluck('statistic_id');
        }

        $departmentId = auth()->user()->department_id; // Or however you determine the department ID
        if (auth()->user()->rule_id == 2) {

            foreach ($Statistics as $statistic) {
                switch ($statistic->name) {
                    case 'الموظفين':
                        $counts[$statistic->name] = User::where('flag', 'employee')->count();
                        $routes[$statistic->name] = route('user.employees', 1);
                        break;
                    case 'المستخدمين':
                        $counts[$statistic->name] = User::where('flag', 'user')->count();
                        $routes[$statistic->name] = route('user.index', 0);

                        break;
                    case 'المجموعات':
                        $counts[$statistic->name] = Groups::count();
                        $routes[$statistic->name] = route('group.view');

                        break;
                    case 'الادارات':
                        $counts[$statistic->name] = departements::count();
                        $routes[$statistic->name] = route('departments.index');

                        break;
                    case 'الاجازات':
                        $counts[$statistic->name] = EmployeeVacation::where('status', 'Approved')->count();
                        $routes[$statistic->name] = route('vacations.list');

                        break;
                    case 'اوامر خدمة':
                        $counts[$statistic->name] = instantmission::count();
                        $routes[$statistic->name] = route('instant_mission.index');

                        break;
                    case 'الصادر':
                        $counts[$statistic->name] = outgoings::count();
                        $routes[$statistic->name] = route('Export.index');

                        break;
                    case 'الوارد':
                        $counts[$statistic->name] = Iotelegram::count();
                        $routes[$statistic->name] = route('iotelegrams.list');

                        break;
                    default:
                        $counts[$statistic->name] = 0; // Default to 0 if no match found
                        $routes[$statistic->name] = 0;

                        break;
                }
            }

            DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');
            $inspector_missions = InspectorMission::whereYear('date', date('Y'))
                ->whereMonth('date', date('m'))
                ->whereNotNull('ids_group_point') // Ensure 'ids_group_point' is not null
                ->groupBy('inspector_id')
                ->get();
            // dd($inspector_missions);

            $group_points = 0;
            $ids_instant_mission = 0;
            $points = 0;
            $uniquePoints = [];

            $temp = 0;
            foreach ($inspector_missions as $mission) {
                // Get the Grouppoint data for the current mission
                $GrouppointData = Grouppoint::whereIn('id', is_array($mission->ids_group_point)
                    ? $mission->ids_group_point
                    : explode(',', $mission->ids_group_point))->pluck('points_ids');

                // Count the number of instant missions
                $ids_instant_mission += count(is_array($mission->ids_instant_mission)
                    ? $mission->ids_instant_mission
                    : explode(',', $mission->ids_instant_mission));

                // Initialize a set to track unique points
                foreach ($GrouppointData as $value) {
                    // Parse the 'points_ids' value into an array
                    $pointsArray = is_array($value) ? $value : explode(',', $value);

                    // Count only unique points
                    foreach ($pointsArray as $point) {
                        if (!in_array($point, $uniquePoints)) {
                            $uniquePoints[] = $point;
                            $points++;
                        }
                    }
                }
            }

            // dd($inspector_missions);

            $violations = Violation::whereYear('created_at', date('Y'))
                ->whereMonth('created_at',  date('m'))
                ->where('status', 1)
                ->count();

            $inspectors = Inspector::whereYear('created_at', date('Y'))
                ->whereMonth('created_at',  date('m'))
                ->count();
            // dd($inspectors);
            // dd($points);
            // dd($violations);
        } else {

            foreach ($Statistics as $statistic) {
                switch ($statistic->name) {
                    case 'الموظفين':
                        $counts[$statistic->name] = User::leftJoin('departements', 'users.department_id', 'departements.id')->where(function ($query) {
                            $query->where('users.department_id', Auth::user()->department_id)
                                ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                        })->where('flag', 'employee')->count();
                        $routes[$statistic->name] = route('user.employees', 1);
                        break;
                    case 'المستخدمين':
                        $counts[$statistic->name] = User::leftJoin('departements', 'users.department_id', 'departements.id')->where(function ($query) {
                            $query->where('users.department_id', Auth::user()->department_id)
                                ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                        })->where('flag', 'user')->count();
                        $routes[$statistic->name] = route('user.index', 0);

                        break;
                    case 'المجموعات':
                        $counts[$statistic->name] = Groups::where('created_departement', Auth::user()->department_id)->count();
                        $routes[$statistic->name] = route('group.view');

                        break;
                    case 'الادارات':
                        $counts[$statistic->name] = departements::where(function ($query) {
                            $query->where('id', Auth::user()->department_id)
                                ->orWhere('parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                        })->count();
                        $routes[$statistic->name] = route('departments.index');

                        break;
                    case 'الاجازات':
                        $counts[$statistic->name] = EmployeeVacation::where('created_departement', Auth::user()->department_id)->where('status', 'Approved')->count();
                        $routes[$statistic->name] = route('vacations.list');

                        break;
                    case 'اوامر خدمة':
                        $counts[$statistic->name] = instantmission::where('created_departement', Auth::user()->department_id)->count();
                        $routes[$statistic->name] = route('instant_mission.index');

                        break;
                    case 'الصادر':
                        $counts[$statistic->name] = outgoings::where('created_department', Auth::user()->department_id)->count();
                        $routes[$statistic->name] = route('Export.index');

                        break;
                    case 'الوارد':
                        $counts[$statistic->name] = Iotelegram::where('created_departement', Auth::user()->department_id)->count();
                        $routes[$statistic->name] = route('iotelegrams.list');

                        break;
                    default:
                        $counts[$statistic->name] = 0; // Default to 0 if no match found
                        $routes[$statistic->name] = 0;

                        break;
                }
            }


            $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id)
                        ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                })
                ->where('status', 1)
                ->count();
            $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')
                ->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id)
                        ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                })->count();

            DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');


            $users_id = User::leftJoin('departements', 'users.department_id', 'departements.id')->where(function ($query) {
                $query->where('users.department_id', Auth::user()->department_id)
                    ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
            })->pluck('users.id')->toArray();
            $inspectors = Inspector::whereIn('user_id', $users_id)->pluck('id')->toArray();
            $inspector_missions = InspectorMission::whereIn('inspector_id', $inspectors)
                ->whereYear('date', date('Y'))
                ->whereMonth('date', date('m'))
                ->whereNotNull('ids_group_point') // Ensure 'ids_group_point' is not null
                ->groupBy('inspector_id')
                ->get();
            // dd($inspector_missions);

            $group_points = 0;
            $ids_instant_mission = 0;
            $points = 0;
            $uniquePoints = [];

            $temp = 0;
            foreach ($inspector_missions as $mission) {
                // Get the Grouppoint data for the current mission
                $GrouppointData = Grouppoint::whereIn('id', is_array($mission->ids_group_point)
                    ? $mission->ids_group_point
                    : explode(',', $mission->ids_group_point))->pluck('points_ids');

                // Count the number of instant missions
                $ids_instant_mission += count(is_array($mission->ids_instant_mission)
                    ? $mission->ids_instant_mission
                    : explode(',', $mission->ids_instant_mission));

                // Initialize a set to track unique points
                foreach ($GrouppointData as $value) {
                    // Parse the 'points_ids' value into an array
                    $pointsArray = is_array($value) ? $value : explode(',', $value);

                    // Count only unique points
                    foreach ($pointsArray as $point) {
                        if (!in_array($point, $uniquePoints)) {
                            $uniquePoints[] = $point;
                            $points++;
                        }
                    }
                }
            }
        }
        if (auth()->user()->rule_id == 2) {

            $Groups = Groups::all();
        } else {
            $Groups = Groups::leftJoin('departements', 'groups.created_departement', 'departements.id')->where(function ($query) {
                $query->where('created_departement', Auth::user()->department_id)
                    ->orwhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
            })->select('groups.*')->get();
        }

        // Initialize cumulative counters for all data points
        $totalViolations = 0;
        $totalInspectors = 0;
        $totalGroupPoints = 0;
        $totalPoints = 0;
        $totalIdsInstantMission = 0;
        $uniquePoints = [];
        $uniqueGroupPoints = [];
        $points2 = 0;
        $inspectors = 0;
        $group_points2 = 0;
        $uniqueInstants = [];
        $ids_instant_mission2 = 0;
        foreach ($Groups as $Group) {
            // Count violations for each group
            $violations2 = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                ->whereBetween('violations.created_at', [date('Y-m-01'), date('Y-m-t')])
                ->where('inspectors.group_id', $Group->id)
                ->where('status', 1)
                ->count();
            $Group['violations'] = $violations2;
            // dd($violations, $Group->id);
            $totalViolations += $violations2;

            // Count inspectors for each group
            $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                ->whereBetween('inspectors.created_at', [date('Y-m-01'), date('Y-m-t')])
                ->where('inspectors.group_id', $Group->id)
                ->count();
            $Group['inspectors'] = $inspectors;
            $totalInspectors += $inspectors;

            // Filter missions by group and department
            $groupedMissions = InspectorMission::where('group_id', $Group->id)
                ->whereBetween('date', [date('Y-m-01'), date('Y-m-t')])
                ->where(function ($query) {
                    $query->whereNotNull('ids_instant_mission')
                        ->orwhereNotNull('ids_group_point'); // Ensure 'ids_group_point' is not null
                });

            DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');

            // dd($groupedMissions->clone()->groupBy('inspector_id')->toSql());
            $forPoints = $groupedMissions->clone()->groupBy('inspector_id')->get();

            // dd($forPoints);
            // Calculate points and missions
            foreach ($forPoints as $inspector_mission) {
                $group_pointsData2 = is_array($inspector_mission->ids_group_point)
                    ? $inspector_mission->ids_group_point
                    : explode(',', $inspector_mission->ids_group_point);

                $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                    ? $inspector_mission->ids_group_point
                    : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');


                foreach ($GrouppointData as $value) {
                    // Parse the 'points_ids' value into an array
                    $pointsArray = is_array($value) ? $value : explode(',', $value);

                    // Count only unique points
                    foreach ($pointsArray as $point) {
                        if (!in_array($point, $uniquePoints)) {
                            $uniquePoints[] = $point;
                            $points2++;
                        }
                    }
                }
                foreach ($group_pointsData2 as $value) {
                    // Parse the 'points_ids' value into an array
                    $pointsDataArray = is_array($value) ? $value : explode(',', $value);

                    // Count only unique points
                    foreach ($pointsDataArray as $point) {
                        if (!in_array($point, $uniqueGroupPoints)) {
                            $uniqueGroupPoints[] = $point;
                            $group_points2++;
                        }
                    }
                }
            }
            $forInstants = $groupedMissions->clone()->get();

            foreach ($forInstants as $inspector_mission) {

                $ids_instant_missionData2 = is_array($inspector_mission->ids_instant_mission)
                    ? $inspector_mission->ids_instant_mission
                    : explode(',', $inspector_mission->ids_instant_mission);
                foreach ($ids_instant_missionData2 as $value) {
                    // Parse the 'points_ids' value into an array
                    $instantsDataArray = is_array($value) ? $value : explode(',', $value);

                    // Count only unique points
                    foreach ($instantsDataArray as $point) {
                        if ($point != "") {
                            if (!in_array($point, $uniqueInstants)) {
                                $uniqueInstants[] = $point;
                                $ids_instant_mission2++;
                            }
                        }
                    }
                }
            }
            $Group['group_points'] = $group_points2;
            $Group['points'] = $points2;
            $Group['ids_instant_mission'] = $ids_instant_mission2;
            // Add to cumulative totals
            $totalGroupPoints += $group_points2;
            $totalPoints += $points2;
            $totalIdsInstantMission += $ids_instant_mission2;
            $points2 = 0;
            $group_points2 = 0;
            $ids_instant_mission2 = 0;
        }

        return view('home.index', get_defined_vars());
    }
    public function filter(Request $request)
    {
        $month = $request->input('month'); // Get the selected month
        $year = $request->input('year'); // Get the selected year
        // Filter data based on selected month and year
        $violations = Violation::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 1)

            ->count();
        $inspectors = Inspector::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();


        DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');
        $inspector_missions = InspectorMission::whereYear('date',  $year)
            ->whereMonth('date', $month)
            ->whereNotNull('ids_group_point') // Ensure 'ids_group_point' is not null
            ->groupBy('inspector_id')
            ->get();
        // dd($inspector_missions);

        $points = 0;
        $uniquePoints = [];

        foreach ($inspector_missions as $mission) {
            // Get the Grouppoint data for the current mission
            $GrouppointData = Grouppoint::whereIn('id', is_array($mission->ids_group_point)
                ? $mission->ids_group_point
                : explode(',', $mission->ids_group_point))->pluck('points_ids');

            // Initialize a set to track unique points
            foreach ($GrouppointData as $value) {
                // Parse the 'points_ids' value into an array
                $pointsArray = is_array($value) ? $value : explode(',', $value);

                // Count only unique points
                foreach ($pointsArray as $point) {
                    if (!in_array($point, $uniquePoints)) {
                        $uniquePoints[] = $point;
                        $points++;
                    }
                }
            }
        }

        // Return JSON response
        return response()->json([
            'violations' => $violations,
            'points' => $points,
            'inspectors' => $inspectors
        ]);
    }
    function searchStatistic(Request $request)
    {

        $Groups = Groups::all();
        $teams = 0;

        // Initialize cumulative counters for all data points
        $totalViolations = 0;
        $totalInspectors = 0;
        $totalGroupPoints = 0;
        $totalPoints = 0;
        $totalIdsInstantMission = 0;
        $points2 = 0;
        $uniquePoints = [];
        $inspectors = 0;
        $group_pointsData2 = 0;
        $group_points2 = 0;
        $uniquegroupPoints = [];
        $ids_instant_mission2 = 0;
        $uniqueInstants = [];
        $ids_instant_mission2 = 0;
        if ($request->group_id && !$request->group_team_id) {


            $teams = GroupTeam::where('group_id', $request->group_id)->get();

            foreach ($teams as $team) {
                $inspectorIds = explode(',', $team->inspector_ids);
                $users_id = Inspector::whereIn('id', $inspectorIds)->pluck('user_id');
                // Count violations for each group
                $violations = Violation::whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('status', 1)
                    ->whereIn('user_id', $users_id)->count();

                $team['violations'] = $violations;
                $totalViolations += $violations;

                // Count inspectors for each group
                $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                    ->leftJoin('group_teams', 'group_teams.group_id', 'inspectors.group_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })
                    ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                    ->where('group_teams.id', $team->id)
                    ->count();

                $team['inspectors'] = $inspectors;
                $totalInspectors += $inspectors;

                DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->where(function ($query) {
                        $query->whereNotNull('ids_instant_mission')
                            ->orwhereNotNull('ids_group_point'); // Ensure 'ids_group_point' is not null
                    })
                    ->where('group_team_id', $team->id)
                    ->where('group_id', $request->group_id);
                // ->get();



                // Calculate points and missions
                $forPoints = $groupedMissions->clone()->groupBy('inspector_id')->get();
                foreach ($forPoints as $inspector_mission) {
                    $group_pointsData2 = is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point);

                    $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');

                    foreach ($GrouppointData as $value) {
                        // Parse the 'points_ids' value into an array
                        $pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($pointsArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniquePoints)) {
                                    $uniquePoints[] = $point;
                                    $points2++;
                                }
                            }
                        }
                    }
                    foreach ($group_pointsData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $group_pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($group_pointsArray as $point) {
                            if ($point != "") {


                                if (!in_array($point, $uniquegroupPoints)) {
                                    $uniquegroupPoints[] = $point;
                                    $group_points2++;
                                }
                            }
                        }
                    }
                }

                $forInstants = $groupedMissions->clone()->get();
                foreach ($forInstants as $inspector_mission) {

                    $ids_instant_missionData2 = is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission);

                    foreach ($ids_instant_missionData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $instantsDataArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($instantsDataArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniqueInstants)) {
                                    $uniqueInstants[] = $point;
                                    $ids_instant_mission2++;
                                }
                            }
                        }
                    }
                }
                $team['group_points'] = $group_points2;
                $team['points'] = $points2;
                $team['ids_instant_mission'] = $ids_instant_mission2;

                // Add to cumulative totals
                $totalGroupPoints += $group_points2;
                $totalPoints += $points2;
                $totalIdsInstantMission += $ids_instant_mission2;
                $points2 = 0;
                $inspectors = 0;
                $group_points2 = 0;
                $ids_instant_mission2 = 0;
                $violations = 0;
            }
        } else if ($request->group_id && $request->group_team_id) {

            $teams = 0;
            $group_team_id = $request->group_team_id;
            $team  = GroupTeam::find($group_team_id);
            $inspector_ids = $team->inspector_ids;
            $inspectorIds = explode(',', $inspector_ids);
            $inspectors = Inspector::whereIn('id', $inspectorIds)->get();

            foreach ($inspectors as $inspector) {

                $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                    ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('status', 1)
                    ->where('inspectors.id', $inspector->id)->count();
                $inspector['violations'] = $violations;
                $totalViolations += $violations;

                // Count inspectors for each group
                $inspectorsCount = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })
                    ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                    ->where('inspectors.id', $inspector->id)
                    ->count();

                $inspector['inspectors'] = $inspectorsCount;
                $totalInspectors += $inspectorsCount;

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->where('inspector_id', $inspector->id)
                    ->get();

                $group_points2 = 0;
                $points2 = 0;
                $ids_instant_mission2 = 0;

                // Calculate points and missions
                foreach ($groupedMissions as $inspector_mission) {
                    $group_points2 += count(is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point));

                    $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');


                    foreach ($GrouppointData as $value) {
                        // Parse the 'points_ids' value into an array
                        $pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($pointsArray as $point) {
                            if (!in_array($point, $uniquePoints)) {
                                $uniquePoints[] = $point;
                                $points2++;
                            }
                        }
                    }
                    $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission));
                }

                $inspector['group_points'] = $group_points2;
                $inspector['points'] = $points2;
                $inspector['ids_instant_mission'] = $ids_instant_mission2;

                // Add to cumulative totals
                $totalGroupPoints += $group_points2;
                $totalPoints += $points2;
                $totalIdsInstantMission += $ids_instant_mission2;
            }
        } else {
            foreach ($Groups as $Group) {
                // Count violations for each group
                $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                    ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('inspectors.group_id', $Group->id)
                    ->where('status', 1)
                    ->count();
                $Group['violations'] = $violations;

                $totalViolations += $violations;

                // Count inspectors for each group
                $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })
                    ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                    ->where('inspectors.group_id', $Group->id)
                    ->count();

                $Group['inspectors'] = $inspectors;
                $totalInspectors += $inspectors;
                DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->where(function ($query) {
                        $query->whereNotNull('ids_instant_mission')
                            ->orwhereNotNull('ids_group_point'); // Ensure 'ids_group_point' is not null
                    })
                    // ->distinct('inspector_id')
                    ->where('group_id', $Group->id);


                $group_points2 = 0;
                $points2 = 0;
                $ids_instant_mission2 = 0;

                // Calculate points and missions
                $forPoints = $groupedMissions->clone()->groupBy('inspector_id')->get();
                foreach ($forPoints as $inspector_mission) {
                    $group_pointsData2 = is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point);

                    $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');

                    foreach ($GrouppointData as $value) {
                        // Parse the 'points_ids' value into an array
                        $pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($pointsArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniquePoints)) {
                                    $uniquePoints[] = $point;
                                    $points2++;
                                }
                            }
                        }
                    }
                    foreach ($group_pointsData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $group_pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($group_pointsArray as $point) {
                            if ($point != "") {


                                if (!in_array($point, $uniquegroupPoints)) {
                                    $uniquegroupPoints[] = $point;
                                    $group_points2++;
                                }
                            }
                        }
                    }
                }


                $forInstants = $groupedMissions->clone()->get();
                foreach ($forInstants as $inspector_mission) {

                    $ids_instant_missionData2 = is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission);

                    foreach ($ids_instant_missionData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $instantsDataArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($instantsDataArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniqueInstants)) {
                                    $uniqueInstants[] = $point;
                                    $ids_instant_mission2++;
                                }
                            }
                        }
                    }
                }

                $Group['group_points'] = $group_points2;
                $Group['points'] = $points2;
                $Group['ids_instant_mission'] = $ids_instant_mission2;

                // Add to cumulative totals
                $totalGroupPoints += $group_points2;
                $totalPoints += $points2;
                $totalIdsInstantMission += $ids_instant_mission2;
            }
        }



        return response()->json([
            'totalGroupPoints' => $totalGroupPoints,
            'totalPoints' => $totalPoints,
            'totalIdsInstantMission' => $totalIdsInstantMission,
            'totalInspectors' => $totalInspectors,
            'totalViolations' => $totalViolations,
            'groups' => $Groups,
            'teams' => $teams,
            'inspectors' => $inspectors
        ]);
    }

    function compareGragh(Request $request)
    {
        $filter_id1 = $request->filter_id1;
        $filter_id2 = $request->filter_id2;
        $type = $request->type;
        // dd($type);

        $totalGroupPoints1 = 0;
        $totalInspectors1 = 0;
        $totalViolations1 = 0;
        $totalIdsInstantMission1 = 0;
        $totalPoints1 = 0;

        $totalGroupPoints2 = 0;
        $totalInspectors2 = 0;
        $totalViolations2 = 0;
        $totalIdsInstantMission2 = 0;
        $totalPoints2 = 0;
        // $uniquegroupPoints = 0;
        if ($type == 'group') {

            $group_points2 = 0;
            $points2 = 0;
            $ids_instant_mission2 = 0;
            $uniqueInstants = [];
            $uniquePoints = [];
            $uniquegroupPoints = [];
            $teams1 = GroupTeam::where('group_id', $filter_id1)->get();

            foreach ($teams1 as $team) {
                $inspectorIds = explode(',', $team->inspector_ids);
                $users_id = Inspector::whereIn('id', $inspectorIds)->pluck('user_id');
                // Count violations for each group
                $violations = Violation::whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('status', 1)
                    ->whereIn('user_id', $users_id)->count();

                $totalViolations1 += $violations;

                // Count inspectors for each group
                $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                    ->leftJoin('group_teams', 'group_teams.group_id', 'inspectors.group_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })
                    ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                    ->where('group_teams.id', $team->id)
                    ->count();

                $totalInspectors1 += $inspectors;

                DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->where(function ($query) {
                        $query->whereNotNull('ids_instant_mission')
                            ->orwhereNotNull('ids_group_point'); // Ensure 'ids_group_point' is not null
                    })
                    ->where('group_team_id', $team->id)
                    ->where('group_id', $request->group_id);
                // ->get();



                // Calculate points and missions
                $forPoints = $groupedMissions->clone()->groupBy('inspector_id')->get();
                foreach ($forPoints as $inspector_mission) {
                    $group_pointsData2 = is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point);

                    $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');

                    foreach ($GrouppointData as $value) {
                        // Parse the 'points_ids' value into an array
                        $pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($pointsArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniquePoints)) {
                                    $uniquePoints[] = $point;
                                    $points2++;
                                }
                            }
                        }
                    }
                    foreach ($group_pointsData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $group_pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($group_pointsArray as $point) {
                            if ($point != "") {


                                if (!in_array($point, $uniquegroupPoints)) {
                                    $uniquegroupPoints[] = $point;
                                    $group_points2++;
                                }
                            }
                        }
                    }
                }

                $forInstants = $groupedMissions->clone()->get();
                foreach ($forInstants as $inspector_mission) {

                    $ids_instant_missionData2 = is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission);

                    foreach ($ids_instant_missionData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $instantsDataArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($instantsDataArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniqueInstants)) {
                                    $uniqueInstants[] = $point;
                                    $ids_instant_mission2++;
                                }
                            }
                        }
                    }
                }

                $totalGroupPoints1 += $group_points2;
                $totalPoints1 += $points2;
                $totalIdsInstantMission1 += $ids_instant_mission2;
            }
            $group_points2 = 0;
            $points2 = 0;
            $ids_instant_mission2 = 0;
            $uniqueInstants = [];
            $uniquePoints = [];
            $uniquegroupPoints = [];
            $teams2 = GroupTeam::where('group_id', $filter_id2)->get();

            foreach ($teams2 as $team) {
                $inspectorIds = explode(',', $team->inspector_ids);
                $users_id = Inspector::whereIn('id', $inspectorIds)->pluck('user_id');
                // Count violations for each group
                $violations = Violation::whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('status', 1)
                    ->whereIn('user_id', $users_id)->count();

                $totalViolations2 += $violations;

                // Count inspectors for each group
                $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                    ->leftJoin('group_teams', 'group_teams.group_id', 'inspectors.group_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })
                    ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                    ->where('group_teams.id', $team->id)
                    ->count();

                $totalInspectors2 += $inspectors;

                DB::statement('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""));');

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->where(function ($query) {
                        $query->whereNotNull('ids_instant_mission')
                            ->orwhereNotNull('ids_group_point'); // Ensure 'ids_group_point' is not null
                    })
                    ->where('group_team_id', $team->id)
                    ->where('group_id', $request->group_id);
                // ->get();



                // Calculate points and missions
                $forPoints = $groupedMissions->clone()->groupBy('inspector_id')->get();
                foreach ($forPoints as $inspector_mission) {
                    $group_pointsData2 = is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point);

                    $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');

                    foreach ($GrouppointData as $value) {
                        // Parse the 'points_ids' value into an array
                        $pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($pointsArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniquePoints)) {
                                    $uniquePoints[] = $point;
                                    $points2++;
                                }
                            }
                        }
                    }
                    foreach ($group_pointsData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $group_pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($group_pointsArray as $point) {
                            if ($point != "") {


                                if (!in_array($point, $uniquegroupPoints)) {
                                    $uniquegroupPoints[] = $point;
                                    $group_points2++;
                                }
                            }
                        }
                    }
                }

                $forInstants = $groupedMissions->clone()->get();
                foreach ($forInstants as $inspector_mission) {

                    $ids_instant_missionData2 = is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission);

                    foreach ($ids_instant_missionData2 as $value) {
                        // Parse the 'points_ids' value into an array
                        $instantsDataArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($instantsDataArray as $point) {
                            if ($point != "") {

                                if (!in_array($point, $uniqueInstants)) {
                                    $uniqueInstants[] = $point;
                                    $ids_instant_mission2++;
                                }
                            }
                        }
                    }
                }

                $totalGroupPoints2 += $group_points2;
                $totalPoints2 += $points2;
                $totalIdsInstantMission2 += $ids_instant_mission2;
            }
        } else if ($type == 'point') {

            $totalViolations1 =  Violation::where('status', 1)->where('flag', 1)->where('point_id', $filter_id1)->count();
            $totalViolations2 =  Violation::where('status', 1)->where('flag', 1)->where('point_id', $filter_id2)->count();
        } else if ($type == 'inspector') {
            $group_points2 = 0;
            $points2 = 0;
            $ids_instant_mission2 = 0;
            $uniquePoints = [];


            $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')
                ->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                ->where('status', 1)
                ->where('inspectors.id', $filter_id1)->count();
            $totalViolations1 += $violations;

            // Count inspectors for each group
            $inspectorsCount = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')
                ->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id)
                        ->orWhere('departements.parent_id', Auth::user()->department_id);
                })
                ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                ->where('inspectors.id', $filter_id1)
                ->count();

            $totalInspectors1 += $inspectorsCount;

            // Filter missions by group and department
            $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                ->where('inspector_id', $filter_id1)
                ->get();



            // Calculate points and missions
            foreach ($groupedMissions as $inspector_mission) {
                $group_points2 += count(is_array($inspector_mission->ids_group_point)
                    ? $inspector_mission->ids_group_point
                    : explode(',', $inspector_mission->ids_group_point));

                $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                    ? $inspector_mission->ids_group_point
                    : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');


                foreach ($GrouppointData as $value) {
                    // Parse the 'points_ids' value into an array
                    $pointsArray = is_array($value) ? $value : explode(',', $value);

                    // Count only unique points
                    foreach ($pointsArray as $point) {
                        if (!in_array($point, $uniquePoints)) {
                            $uniquePoints[] = $point;
                            $points2++;
                        }
                    }
                }
                $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                    ? $inspector_mission->ids_instant_mission
                    : explode(',', $inspector_mission->ids_instant_mission));
            }

            // Add to cumulative totals
            $totalGroupPoints1 += $group_points2;
            $totalPoints1 += $points2;
            $totalIdsInstantMission1 += $ids_instant_mission2;

            $group_points2 = 0;
            $points2 = 0;
            $ids_instant_mission2 = 0;
            $uniquePoints = [];

            $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')
                ->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                ->where('status', 1)
                ->where('inspectors.id', $filter_id1)->count();
            $totalViolations2 += $violations;

            // Count inspectors for each group
            $inspectorsCount = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')
                ->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id)
                        ->orWhere('departements.parent_id', Auth::user()->department_id);
                })
                ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                ->where('inspectors.id', $filter_id1)
                ->count();

            $totalInspectors2 += $inspectorsCount;

            // Filter missions by group and department
            $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                ->where('inspector_id', $filter_id1)
                ->get();



            // Calculate points and missions
            foreach ($groupedMissions as $inspector_mission) {
                $group_points2 += count(is_array($inspector_mission->ids_group_point)
                    ? $inspector_mission->ids_group_point
                    : explode(',', $inspector_mission->ids_group_point));

                $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                    ? $inspector_mission->ids_group_point
                    : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');


                foreach ($GrouppointData as $value) {
                    // Parse the 'points_ids' value into an array
                    $pointsArray = is_array($value) ? $value : explode(',', $value);

                    // Count only unique points
                    foreach ($pointsArray as $point) {
                        if (!in_array($point, $uniquePoints)) {
                            $uniquePoints[] = $point;
                            $points2++;
                        }
                    }
                }
                $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                    ? $inspector_mission->ids_instant_mission
                    : explode(',', $inspector_mission->ids_instant_mission));
            }

            // Add to cumulative totals
            $totalGroupPoints2 += $group_points2;
            $totalPoints2 += $points2;
            $totalIdsInstantMission2 += $ids_instant_mission2;
        } else if ($type == 'group_point') {

            $totalViolations1 =  Violation::where('status', 1)->where('flag', 1)->where('point_id', $filter_id1)->count();
            $totalViolations2 =  Violation::where('status', 1)->where('flag', 1)->where('point_id', $filter_id2)->count();
        } else if ($type == 'team') {
            $group_points2 = 0;
            $points2 = 0;
            $ids_instant_mission2 = 0;
            $uniquePoints = [];

            $filter_id1 = $request->filter_id1;
            $team  = GroupTeam::find($filter_id1);
            $inspector_ids = $team->inspector_ids;
            $inspectorIds = explode(',', $inspector_ids);
            $inspectors = Inspector::whereIn('id', $inspectorIds)->get();

            foreach ($inspectors as $inspector) {

                $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                    ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('status', 1)
                    ->where('inspectors.id', $inspector->id)->count();
                $totalViolations1 += $violations;

                // Count inspectors for each group
                $inspectorsCount = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })
                    ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                    ->where('inspectors.id', $inspector->id)
                    ->count();

                $totalInspectors1 += $inspectorsCount;

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->where('inspector_id', $inspector->id)
                    ->get();


                // Calculate points and missions
                foreach ($groupedMissions as $inspector_mission) {
                    $group_points2 += count(is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point));

                    $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');


                    foreach ($GrouppointData as $value) {
                        // Parse the 'points_ids' value into an array
                        $pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($pointsArray as $point) {
                            if (!in_array($point, $uniquePoints)) {
                                $uniquePoints[] = $point;
                                $points2++;
                            }
                        }
                    }
                    $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission));
                }

                // Add to cumulative totals
                $totalGroupPoints1 += $group_points2;
                $totalPoints1 += $points2;
                $totalIdsInstantMission1 += $ids_instant_mission2;
                // }
            }
            $group_points2 = 0;
            $points2 = 0;
            $ids_instant_mission2 = 0;
            $uniquePoints = [];

            $filter_id2 = $request->filter_id2;
            $team  = GroupTeam::find($filter_id2);
            $inspector_ids = $team->inspector_ids;
            $inspectorIds = explode(',', $inspector_ids);
            $inspectors = Inspector::whereIn('id', $inspectorIds)->get();

            foreach ($inspectors as $inspector) {

                $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                    ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('status', 1)
                    ->where('inspectors.id', $inspector->id)->count();
                $totalViolations2 += $violations;

                // Count inspectors for each group
                $inspectorsCount = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })
                    ->whereBetween('inspectors.created_at', [$request->date_from, $request->date_to])
                    ->where('inspectors.id', $inspector->id)
                    ->count();

                $totalInspectors2 += $inspectorsCount;

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->where('inspector_id', $inspector->id)
                    ->get();


                // Calculate points and missions
                foreach ($groupedMissions as $inspector_mission) {
                    $group_points2 += count(is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point));

                    $GrouppointData = Grouppoint::whereIn('id', is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');


                    foreach ($GrouppointData as $value) {
                        // Parse the 'points_ids' value into an array
                        $pointsArray = is_array($value) ? $value : explode(',', $value);

                        // Count only unique points
                        foreach ($pointsArray as $point) {
                            if (!in_array($point, $uniquePoints)) {
                                $uniquePoints[] = $point;
                                $points2++;
                            }
                        }
                    }
                    $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission));
                }

                // Add to cumulative totals
                $totalGroupPoints2 += $group_points2;
                $totalPoints2 += $points2;
                $totalIdsInstantMission2 += $ids_instant_mission2;
                // }
            }
        }

        return response()->json([
            'totalViolations1' => $totalViolations1,
            'totalGroupPoints1' => $totalGroupPoints1,
            'totalInspectors1' => $totalInspectors1,
            'totalIdsInstantMission1' => $totalIdsInstantMission1,
            'totalPoints1' => $totalPoints1,
            'totalViolations2' => $totalViolations2,
            'totalGroupPoints2' => $totalGroupPoints2,
            'totalInspectors2' => $totalInspectors2,
            'totalIdsInstantMission2' => $totalIdsInstantMission2,
            'totalPoints2' => $totalPoints2,

        ]);
    }
}
