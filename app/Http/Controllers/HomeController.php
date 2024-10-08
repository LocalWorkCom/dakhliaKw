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
use App\Models\Statistic;
use App\Models\UserStatistic;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    //
    public function index(Request $request)
    {
        $Statistics = Statistic::all();
        $counts = [];
        $UserStatistic = UserStatistic::where('user_id', Auth::user()->id)->where('checked', 1)->pluck('statistic_id');


        $departmentId = auth()->user()->department_id; // Or however you determine the department ID
        if (auth()->user()->rule_id == 2) {

            foreach ($Statistics as $statistic) {
                switch ($statistic->name) {
                    case 'الموظفين':
                        $counts[$statistic->name] = User::where('flag', 'employee')->count();
                        break;
                    case 'المستخدمين':
                        $counts[$statistic->name] = User::where('flag', 'user')->count();
                        break;
                    case 'المجموعات':
                        $counts[$statistic->name] = Groups::count();
                        break;
                    case 'الادارات':
                        $counts[$statistic->name] = departements::count();
                        break;
                    case 'الاجازات':
                        $counts[$statistic->name] = EmployeeVacation::where('status', 'Approved')->count();
                        break;
                    case 'اوامر خدمة':
                        $counts[$statistic->name] = instantmission::count();
                        break;
                    case 'الصادر':
                        $counts[$statistic->name] = outgoings::count();
                        break;
                    case 'الوارد':
                        $counts[$statistic->name] = Iotelegram::count();
                        break;
                    default:
                        $counts[$statistic->name] = 0; // Default to 0 if no match found
                        break;
                }
            }

            $inspector_missions = InspectorMission::whereYear('date', date('Y'))
                ->whereMonth('date',  date('m'))
                ->distinct('inspector_id')
                ->get();

            $group_points = 0;
            $ids_instant_mission = 0;
            $points = 0;

            foreach ($inspector_missions as $mission) {

                $group_points += count(is_array($mission->ids_group_point)
                    ? $mission->ids_group_point
                    : explode(',', $mission->ids_group_point));
                if ($mission->ids_group_point) {
                    // dd($mission);
                }
                $GrouppointData = Grouppoint::whereIn('id', is_array($mission->ids_group_point)
                    ? $mission->ids_group_point
                    : explode(',', $mission->ids_group_point))->pluck('points_ids');
                if ($mission->ids_group_point) {

                    // dd($GrouppointData);
                }
                $ids_instant_mission += count(is_array($mission->ids_instant_mission)
                    ? $mission->ids_instant_mission
                    : explode(',', $mission->ids_instant_mission));
                foreach ($GrouppointData as $key => $value) {
                    $points +=
                        sizeof($value);
                }
                if ($mission->ids_group_point) {
                    // dd($points);

                }
            }

            $violations = Violation::whereYear('created_at', date('Y'))
                ->whereMonth('created_at',  date('m'))
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
                        $counts[$statistic->name] = User::where('flag', 'employee')->count();
                        break;
                    case 'المستخدمين':
                        $counts[$statistic->name] = User::where('flag', 'user')->count();
                        break;
                    case 'المجموعات':
                        $counts[$statistic->name] = Groups::where('created_departement', Auth::user()->department_id)->count();
                        break;
                    case 'الادارات':
                        $counts[$statistic->name] = departements::where(function ($query) {
                            $query->where('id', Auth::user()->department_id)
                                ->orWhere('parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                        })->count();
                        break;
                    case 'الاجازات':
                        $counts[$statistic->name] = EmployeeVacation::where('created_departement', Auth::user()->department_id)->where('status', 'Approved')->count();
                        break;
                    case 'اوامر خدمة':
                        $counts[$statistic->name] = instantmission::where('created_departement', Auth::user()->department_id)->count();
                        break;
                    case 'الصادر':
                        $counts[$statistic->name] = outgoings::where('created_department', Auth::user()->department_id)->count();
                        break;
                    case 'الوارد':
                        $counts[$statistic->name] = Iotelegram::where('created_departement', Auth::user()->department_id)->count();
                        break;
                    default:
                        $counts[$statistic->name] = 0; // Default to 0 if no match found
                        break;
                }
            }

            // $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
            //     ->leftJoin('departements', 'users.department_id', 'departements.id')->where(function ($query) {
            //         $query->where('users.department_id', Auth::user()->department_id)
            //             ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
            //     })->count();
            // $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
            //     ->leftJoin('departements', 'users.department_id', 'departements.id')
            //     ->where(function ($query) {
            //         $query->where('users.department_id', Auth::user()->department_id)
            //             ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
            //     })->count();

            // //filter by department
            // $inspector_missions = InspectorMission::whereYear('date', date('Y'))
            //     ->whereMonth('date',  date('m'))
            //     ->get();

            // $group_points = 0;
            // $points = 0;
            // $ids_instant_mission = 0;

            // $groupedMissions = $inspector_missions->groupBy('inspector_id');

            // foreach ($groupedMissions as $inspector_id => $missions) {
            //     foreach ($missions as $inspector_mission) {
            //         $group_points += count(explode(',', $inspector_mission->ids_group_point));
            //         $GrouppointData = Grouppoint::whereIn('id', explode(',', $inspector_mission->ids_group_point))->pluck('points_ids');
            //         foreach ($GrouppointData as $key => $value) {
            //             $points += count(explode(',', $value->points_ids));
            //         }
            //         $ids_instant_mission += count(explode(',', $inspector_mission->ids_instant_mission));
            //     }
            // }
        }

        $Groups = Groups::all();

        // Initialize cumulative counters for all data points
        $totalViolations = 0;
        $totalInspectors = 0;
        $totalGroupPoints = 0;
        $totalPoints = 0;
        $totalIdsInstantMission = 0;

        foreach ($Groups as $Group) {
            // Count violations for each group
            $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')
                ->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id)
                        ->orWhere('departements.parent_id', Auth::user()->department_id);
                })
                ->where('inspectors.group_id', $Group->id)
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
                ->where('inspectors.group_id', $Group->id)
                ->count();
            $Group['inspectors'] = $inspectors;
            $totalInspectors += $inspectors;

            // Filter missions by group and department
            $groupedMissions = InspectorMission::where('group_id', $Group->id)
                ->whereYear('date', date('Y'))
                ->whereMonth('date', date('m'))
                ->distinct('inspector_id')
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
                    $points2 += count($value);
                }

                $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                    ? $inspector_mission->ids_instant_mission
                    : explode(',', $inspector_mission->ids_instant_mission));
            }

            $Group['group_points'] = $group_points2;
            $Group['points'] = $points2;
            $Group['ids_instant_mission'] = $ids_instant_mission2;

            // Add to cumulative totals
            $totalGroupPoints += $group_points2;
            $totalPoints += $points2;
            $totalIdsInstantMission += $ids_instant_mission2;
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
            ->count();
        $inspectors = Inspector::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $inspector_missions = InspectorMission::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->distinct('inspector_id')
            ->get();

        $group_points = 0;
        $ids_instant_mission = 0;


        foreach ($inspector_missions as  $missions) {
            $group_points += count(is_array($missions->ids_group_point)
                ? $missions->ids_group_point
                : explode(',', $missions->ids_group_point));
            $ids_instant_mission += count(is_array($missions->ids_instant_mission)
                ? $missions->ids_instant_mission
                : explode(',', $missions->ids_instant_mission));
        }

        $points = $ids_instant_mission + $group_points;


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

        // Initialize cumulative counters for all data points
        $totalViolations = 0;
        $totalInspectors = 0;
        $totalGroupPoints = 0;
        $totalPoints = 0;
        $totalIdsInstantMission = 0;
        if ($request->group_id && !$request->group_team_id) {


            $teams = GroupTeam::where('group_id', $request->group_id)->get();
            $inspectors = 0;
            foreach ($teams as $team) {
                // Count violations for each group
                $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                    ->leftJoin('inspectors', 'inspectors.user_id', 'users.id')
                    ->leftJoin('group_teams', 'group_teams.group_id', 'inspectors.group_id')
                    ->leftJoin('departements', 'users.department_id', 'departements.id')
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
                    ->where('group_teams.id', $team->id)->count();
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

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->distinct('inspector_id')
                    ->where('group_team_id', $team->id)
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
                        $points2 += count($value);
                    }

                    $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission));
                }

                $team['group_points'] = $group_points2;
                $team['points'] = $points2;
                $team['ids_instant_mission'] = $ids_instant_mission2;

                // Add to cumulative totals
                $totalGroupPoints += $group_points2;
                $totalPoints += $points2;
                $totalIdsInstantMission += $ids_instant_mission2;
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
                    ->where(function ($query) {
                        $query->where('users.department_id', Auth::user()->department_id)
                            ->orWhere('departements.parent_id', Auth::user()->department_id);
                    })->whereBetween('violations.created_at', [$request->date_from, $request->date_to])
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
                        $points2 += count($value);
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
                    ->where('inspectors.group_id', $Group->group_id)->count();
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
                    ->where('inspectors.group_id', $Group->group_id)
                    ->count();

                $Group['inspectors'] = $inspectors;
                $totalInspectors += $inspectors;

                // Filter missions by group and department
                $groupedMissions = InspectorMission::whereBetween('date', [$request->date_from, $request->date_to])
                    ->distinct('inspector_id')
                    ->where('group_id', $Group->group_id)
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
                        $points2 += count($value);
                    }

                    $ids_instant_mission2 += count(is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission));
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
}
