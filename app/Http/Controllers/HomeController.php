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
            $points = Grouppoint::count();
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

            $points = Grouppoint::count();
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
}
