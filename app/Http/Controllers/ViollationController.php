<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\grade;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\Violation;
use App\Models\instantmission;
use App\Models\ViolationTypes;
use App\Models\InspectorMission;
use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\WorkingTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Groups;

use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\AbsenceEmployee;
use Yajra\DataTables\DataTables;

class ViollationController extends Controller
{
    //
    public function index(Request $request)
    {
        $groups = Groups::all();
        $inspectors_group = Inspector::where('department_id',Auth()->user()->department_id)->select('group_id')
        ->groupBy('group_id')->pluck('group_id');
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $groups = Groups::all();
            $groupTeams = GroupTeam::all();
            $inspectors = Inspector::all();
         }
         else
         {
            $groups = Groups::whereIn('id', $inspectors_group)->get();
            // dd($groups);
            $groupTeams = GroupTeam::whereIn('group_id', $inspectors_group)->get();
            $inspectors = Inspector::where('department_id',Auth()->user()->department_id)->get();
         }
         $date=$request->date;
         $group=$request->group;
         $team=$request->team;
         $inspector=$request->inspector;
        return view('violations.index',compact('groups','groupTeams','inspectors','date','group','team','inspector'));
    }
    public function getviolations(Request $request)
    {
        $first = Absence::Join('points','points.id','absences.point_id')->SelectRaw('absences.id,points.`name` as name,CONCAT_WS("\n\r",CONCAT_WS(":","اجمالى القوة",absences.total_number),CONCAT_WS(":","العدد الفعلي",absences.actual_number)) as ViolationType,"غياب" as Type');

        $data=Violation::leftJoin('grades','grades.id','violations.grade')->SelectRaw("violations.id,CONCAT_WS('/',violations.name,grades.name)  as name,(Select GROUP_CONCAT(violation_type.`name`) from violation_type where FIND_IN_SET(violation_type.id,violations.violation_type)) AS ViolationType,IF(flag=1,'مخالفة سلوك انظباطي','مخالفة مباني') as Type")->union($first);

    /*     foreach ($data as $item) {
            $item->type_names = departements::whereIn('id', $item->type_id)->pluck('name')->implode(', ');
        } */

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $Type ="$row->Type";
                $name ="$row->name";
                $ViolationType ="$row->ViolationType";
                $typesJson = json_encode($row->type_id); // Ensure this is an array

                // $edit_permission = null;
                // $show_permission = null ;
                // if (Auth::user()->hasPermission('edit item')) {
                    $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;" onclick="#"><i class="fa fa-eye"></i> مشاهدة</a>';
                    // }
                // if (Auth::user()->hasPermission('view item')) {
                // $show_permission = '<a class="btn btn-sm" style="background-color: #274373;"  href=' . route('violations.show', $row->id) . '> <i class="fa fa-eye"></i>عرض</a>';
                // }
                return  $edit_permission;
            })
           
            ->rawColumns(['action'])
            ->make(true);

    }

}
