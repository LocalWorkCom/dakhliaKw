<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\outgoings;
use App\Models\Iotelegram;
use App\Models\departements;
use App\Models\EmployeeVacation;
use App\Models\Grouppoint;
use App\Models\Groups;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\instantmission;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $departmentId = auth()->user()->department_id; // Or however you determine the department ID
        if (auth()->user()->rule_id == 2) {
            $empCount = User::where('flag', 'employee')->count();
            $userCount = User::where('flag', 'user')->count();
            $depCount = departements::count();
            $outCount = outgoings::count();
            $ioCount = Iotelegram::count();
            $groups = Groups::count();
            $instantmissions = instantmission::count();
            $employeeVacation = EmployeeVacation::where('status', 'Approved')->count();
            $violations = Violation::count();
            $inspector_missions = InspectorMission::whereYear('date', date('Y'))
                ->whereMonth('date',  date('m'))
                ->get();

            $group_points = 0;
            $ids_instant_mission = 0;

            $groupedMissions = $inspector_missions->groupBy('inspector_id');

            foreach ($groupedMissions as $inspector_id => $missions) {
                foreach ($missions as $inspector_mission) {


                    $group_points += count(is_array($inspector_mission->ids_group_point)
                        ? $inspector_mission->ids_group_point
                        : explode(',', $inspector_mission->ids_group_point));
                    $ids_instant_mission += count(is_array($inspector_mission->ids_instant_mission)
                        ? $inspector_mission->ids_instant_mission
                        : explode(',', $inspector_mission->ids_instant_mission));
                }
            }

            $points = $ids_instant_mission + $group_points;
            $inspectors = Inspector::count();
        } else {
            $empCount = User::where('flag', 'employee')->count();
            $userCount = User::where('flag', 'user')->count();
            $depCount = departements::where(function ($query) {
                $query->where('id', Auth::user()->department_id)
                    ->orWhere('parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
            })->count();
            $outCount = outgoings::where('created_department', Auth::user()->department_id)->count();
            $ioCount = Iotelegram::where('created_departement', Auth::user()->department_id)->count();
            $groups = Groups::where('created_departement', Auth::user()->department_id)->count();
            $instantmissions = instantmission::where('created_departement', Auth::user()->department_id)->count();
            $employeeVacation = EmployeeVacation::where('created_departement', Auth::user()->department_id)->where('status', 'Approved')->count();
            $violations = Violation::leftJoin('users', 'users.id', 'violations.user_id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id)
                        ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                })->count();
            $inspectors = Inspector::leftJoin('users', 'users.id', 'inspectors.user_id')
                ->leftJoin('departements', 'users.department_id', 'departements.id')
                ->where(function ($query) {
                    $query->where('users.department_id', Auth::user()->department_id)
                        ->orWhere('departements.parent_id', Auth::user()->department_id); // Include rows where 'rule_id' is null
                })->count();

            //filter by department
            $inspector_missions = InspectorMission::whereYear('date', date('Y'))
                ->whereMonth('date',  date('m'))
                ->get();

            $group_points = 0;
            $ids_instant_mission = 0;

            $groupedMissions = $inspector_missions->groupBy('inspector_id');

            foreach ($groupedMissions as $inspector_id => $missions) {
                foreach ($missions as $inspector_mission) {
                    $group_points += count(explode(',', $inspector_mission->ids_group_point));
                    $ids_instant_mission += count(explode(',', $inspector_mission->ids_instant_mission));
                }
            }

            $points = $ids_instant_mission + $group_points;
        }

        // if (!Auth::check()) {
        //     return redirect()->route('login');
        // }

        // Check if the previous URL matches
        // if (url()->previous() === route('reset_password')) {
        //     return redirect()->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
        // }

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
            ->get();

        $group_points = 0;
        $ids_instant_mission = 0;

        $groupedMissions = $inspector_missions->groupBy('inspector_id');

        foreach ($groupedMissions as $inspector_id => $missions) {
            foreach ($missions as $inspector_mission) {
                $group_points += count(explode(',', $inspector_mission->ids_group_point));
                $ids_instant_mission += count(explode(',', $inspector_mission->ids_instant_mission));
            }
        }

        $points = $ids_instant_mission + $group_points;


        // Return JSON response
        return response()->json([
            'violations' => $violations,
            'points' => $points,
            'inspectors' => $inspectors
        ]);
    }
}
