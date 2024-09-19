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
        $inspectors_group = Inspector::where('department_id', Auth()->user()->department_id)->select('group_id')
            ->groupBy('group_id')->pluck('group_id');
        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $groups = Groups::all();
            $groupTeams = GroupTeam::all();
            $inspectors = Inspector::all();
        } else {
            $groups = Groups::whereIn('id', $inspectors_group)->get();
            // dd($groups);
            $groupTeams = GroupTeam::whereIn('group_id', $inspectors_group)->get();
            $inspectors = Inspector::where('department_id', Auth()->user()->department_id)->get();
        }
        $date = $request->date;
        $group = $request->group;
        $team = $request->team;
        $inspector = $request->inspector;
        return view('violations.index', compact('groups', 'groupTeams', 'inspectors', 'date', 'group', 'team', 'inspector'));
    }
    public function getviolations(Request $request)
    {
        $date = $request->date;
        $group = $request->group;
        $team = $request->team;
        $inspector = $request->inspector;

        $first = Absence::leftJoin('points', 'points.id', 'absences.point_id')->SelectRaw('absences.id,points.`name` as name,CONCAT_WS("\n\r",CONCAT_WS(":","اجمالى القوة",absences.total_number),CONCAT_WS(":","العدد الفعلي",absences.actual_number)) as ViolationType,"غياب" as Type,"0" as mode')->leftJoin('inspector_mission', 'inspector_mission.id', 'absences.mission_id');
        if (isset($date) && $date != '-1')
            $first->where('absences.date', $date);
        if (isset($group) && $group != '-1')
            $first->where('group_id', $group);
        if (isset($team) && $team != '-1')
            $first->where('group_team_id', $team);
        if (isset($inspector) && $inspector != '-1')
            $first->where('absences.inspector_id', $inspector);

        $data = Violation::leftJoin('grades', 'grades.id', 'violations.grade')->SelectRaw("violations.id,CONCAT_WS('/',violations.name,grades.name)  as name,(Select GROUP_CONCAT(violation_type.`name`) from violation_type where FIND_IN_SET(violation_type.id,violations.violation_type)) AS ViolationType,IF(violations.flag=1,'مخالفة سلوك انظباطي','مخالفة مباني') as Type,'1' as mode")->leftJoin('inspector_mission', 'inspector_mission.id', 'violations.mission_id');

        if (isset($date) && $date != '-1')
            $data->where('inspector_mission.date', $date);
        if (isset($group) && $group != '-1')
            $data->where('inspector_mission.group_id', $group);
        if (isset($team) && $team != '-1')
            $data->where('inspector_mission.group_team_id', $group);
        if (isset($inspector) && $inspector != '-1')
            $data->Join('inspectors', 'inspectors.user_id', 'violations.user_id')->where('inspectors.id', $inspector);
        $data->union($first);
        //dd($data);

        /*     foreach ($data as $item) {
            $item->type_names = departements::whereIn('id', $item->type_id)->pluck('name')->implode(', ');
        } */

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $Type = "$row->Type";
                $name = "$row->name";
                $ViolationType = "$row->ViolationType";
                $typesJson = json_encode($row->type_id); // Ensure this is an array


                // $edit_permission = null;
                // $show_permission = null ;
                // if (Auth::user()->hasPermission('edit item')) {
                $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;" href="' . route('violations.details', ['type' => $row->mode, 'id' => $row->id]) . '"><i class="fa fa-eye"></i> مشاهدة</a>';
                // }
                // if (Auth::user()->hasPermission('view item')) {
                // $show_permission = '<a class="btn btn-sm" style="background-color: #274373;"  href=' . route('violations.show', $row->id) . '> <i class="fa fa-eye"></i>عرض</a>';
                // }
                return  $edit_permission;
            })

            ->rawColumns(['action'])
            ->make(true);
    }
    public function violation_detail(Request $request, $type, $id)
    {
        if ($type == 0) //absence
        {
            $title = "سجل غياب";
            $data = Absence::leftJoin('points', 'points.id', 'absences.point_id')->leftJoin('inspector_mission', 'inspector_mission.id', 'absences.mission_id')->where('absences.id', $id)->first();
            $details = AbsenceEmployee::with('absenceType', 'gradeName')->where('absences_id', $id)->get();
            //dd($details);
        } else { //Violation
            $title = "سجل مخالفات";
            $data = Violation::leftJoin('grades', 'grades.id', 'violations.grade')->SelectRaw("image, violations.id,CONCAT_WS('/',violations.name,grades.name)  as name,(Select GROUP_CONCAT(violation_type.`name`) from violation_type where FIND_IN_SET(violation_type.id,violations.violation_type)) AS ViolationType,IF(violations.flag=1,'مخالفة سلوك انظباطي','مخالفة مباني') as Type")->leftJoin('inspector_mission', 'inspector_mission.id', 'violations.mission_id')->where('violations.id', $id)->first();
            $details = [];
            
        }
        return view('violations.details', compact('type', 'id', 'title', 'data', 'details'));
    }
}
