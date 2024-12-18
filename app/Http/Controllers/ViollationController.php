<?php

namespace App\Http\Controllers;

use App\Models\DungeonInfo;
use App\Models\PointContent;
use App\Models\WeaponInfo;
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
use App\Models\paperTransaction;
use Yajra\DataTables\DataTables;

class ViollationController extends Controller
{
    //
    public function index(Request $request)
    {
        $userDepartmentId = Auth::user()->department_id;
        $isAdmin = in_array(Auth::user()->rule->name, ['localworkadmin', 'superadmin']);

        // Fetch groups and inspectors based on user role
        if ($isAdmin) {
            $groups = Groups::all();
            $groupTeams = GroupTeam::all();
            $inspectors = Inspector::all();
        } else {
            $inspectors_group = Inspector::with('user')
                ->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })
                ->select('group_id')
                ->groupBy('group_id')
                ->pluck('group_id');

            $groups = Groups::whereIn('id', $inspectors_group)->get();
            $groupTeams = GroupTeam::whereIn('group_id', $inspectors_group)->get();
            $inspectors = Inspector::with('user')
                ->where('flag', 0)
                ->whereHas('user', function ($query) use ($userDepartmentId) {
                    $query->where('department_id', $userDepartmentId);
                })
                ->get();
        }

        $date = $request->date;
        $now = date('Y-m-d');
        $allDate = '1';
        if (isset($request->date)) {
            if ($now != $date) {
                $allDate = '0';
            }
        }
        //dd($now);
        $group = $request->group;
        $team = $request->team;
        $inspector = $request->inspector;

        return view('violations.index', compact('groups', 'groupTeams', 'inspectors', 'date', 'group', 'team', 'inspector', 'allDate'));
    }

    public function getViolations(Request $request)
    {
        $date = $request->date;
        $group = $request->group;
        $team = $request->team;
        $inspector = $request->inspector;
        // dd($request);
        // Base query for paper transactions
        $second = paperTransaction::with(['inspector', 'point'])
            ->selectRaw('paper_transactions.id, "معاملات ورقيه" as name, CONCAT_WS("\n\r", CONCAT_WS(":", "رقم القيد", paper_transactions.registration_number), CONCAT_WS(":", "رقم الأحوال", paper_transactions.civil_number)) as ViolationType, "معاملات ورقيه" as Type, "2" as mode')
            ->leftJoin('inspector_mission', 'inspector_mission.id', 'paper_transactions.mission_id');
        $third = PointContent::with(['inspector', 'point'])->leftJoin('points', 'points.id', '=', 'point_contents.point_id')
            ->selectRaw('point_contents.id,  points.name as name, CONCAT_WS("\n\r", CONCAT_WS(":", "عدد الالات", point_contents.mechanisms_num), CONCAT_WS(":", "عدد الالات التصوير", point_contents.cams_num)) as ViolationType, "تفتيش نقطه" as Type, "3" as mode')
            ->leftJoin('inspector_mission', 'inspector_mission.id', 'point_contents.mission_id');
        // Apply filters for paper transactions
        $second->when($date && $date != '-1', function ($query) use ($date) {
            return $query->where('paper_transactions.date', $date); // Specify table
        });

        $second->when($group && $group != '-1', function ($query) use ($group) {
            $inspectors = GroupTeam::where('group_id', $group)->pluck('inspector_ids')->flatten();
            return $query->whereIn('paper_transactions.inspector_id', $inspectors); // Specify table
        });

        $second->when($team && $team != '-1', function ($query) use ($team) {
            $inspectors = GroupTeam::where('id', $team)->pluck('inspector_ids')->flatten();
            return $query->whereIn('paper_transactions.inspector_id', $inspectors); // Specify table
        });

        $second->when($inspector && $inspector != '-1', function ($query) use ($inspector) {
            return $query->where('paper_transactions.inspector_id', $inspector); // Specify table
        });
        // Apply filters for paper transactions
        $third->when($date && $date != '-1', function ($query) use ($date) {
            return $query->whereDate('point_contents.created_at', $date); // Specify table
        });

        $third->when($group && $group != '-1', function ($query) use ($group) {
            $inspectors = GroupTeam::where('group_id', $group)->pluck('inspector_ids')->flatten();
            return $query->whereIn('point_contents.inspector_id', $inspectors); // Specify table
        });

        $third->when($team && $team != '-1', function ($query) use ($team) {
            $inspectors = GroupTeam::where('id', $team)->pluck('inspector_ids')->flatten();
            return $query->whereIn('point_contents.inspector_id', $inspectors); // Specify table
        });

        $third->when($inspector && $inspector != '-1', function ($query) use ($inspector) {
            return $query->where('point_contents.inspector_id', $inspector); // Specify table
        });
        // Base query for absences
        $first = Absence::leftJoin('points', 'points.id', 'absences.point_id')
            ->selectRaw('absences.id, points.`name` as name, CONCAT_WS("\n\r", CONCAT_WS(":", "اجمالى القوة", absences.total_number), CONCAT_WS(":", "العدد الفعلي", absences.actual_number)) as ViolationType, "غياب" as Type, "0" as mode')
            ->leftJoin('inspector_mission', 'inspector_mission.id', 'absences.mission_id');

        // Apply filters for absences
        $first->when($date && $date != '-1', function ($query) use ($date) {
            return $query->where('absences.date', $date); // Specify table
        });

        // $first->when($group && $group != '-1', function ($query) use ($group) {
        //     return $query->where('absences.group_id', $group); // Specify table
        // });
        $first->when($group && $group != '-1', function ($query) use ($group) {
            $inspectors = GroupTeam::where('group_id', $group)->pluck('inspector_ids')->flatten();
            return $query->whereIn('absences.inspector_id', $inspectors); // Specify table
        });

        $first->when($team && $team != '-1', function ($query) use ($team) {
            $inspectors = GroupTeam::where('id', $team)->pluck('inspector_ids')->flatten();
            return $query->whereIn('absences.inspector_id', $inspectors); // Specify table
        });
        // $first->when($team && $team != '-1', function ($query) use ($team) {
        //     return $query->where('absences.group_team_id', $team); // Specify table
        // });

        $first->when($inspector && $inspector != '-1', function ($query) use ($inspector) {
            return $query->where('absences.inspector_id', $inspector); // Specify table
        });

        // Base query for violations
        $data = Violation::leftJoin('grades', 'grades.id', 'violations.grade')
            ->selectRaw("violations.id, CONCAT_WS('/', violations.name, grades.name) as name, (SELECT GROUP_CONCAT(violation_type.`name`) FROM violation_type WHERE FIND_IN_SET(violation_type.id, violations.violation_type)) AS ViolationType, IF(violations.flag=1, 'مخالفة سلوك انضباطي', 'مخالفة مباني') as Type, '1' as mode")
            ->leftJoin('inspector_mission', 'inspector_mission.id', 'violations.mission_id');

        // Apply filters for violations
        $data->when($date && $date != '-1', function ($query) use ($date) {
            return $query->where('inspector_mission.date', $date); // Specify table
        });

        $data->when($group && $group != '-1', function ($query) use ($group) {
            return $query->where('inspector_mission.group_id', $group); // Specify table
        });

        $data->when($team && $team != '-1', function ($query) use ($team) {
            return $query->where('inspector_mission.group_team_id', $team); // Specify table
        });

        $data->when($inspector && $inspector != '-1', function ($query) use ($inspector) {
            return $query->join('inspectors', 'inspectors.user_id', 'violations.user_id')
                ->where('inspectors.id', $inspector); // Specify table
        });

        // Combine queries
        $data = $data->union($first)->union($second)->union($third);

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '<a class="btn btn-sm" style="background-color: #F7AF15;" href="' . route('violations.details', ['type' => $row->mode, 'id' => $row->id]) . '"><i class="fa fa-eye"></i> مشاهدة</a>';
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
            $details_wepon=  [];
        } elseif ($type == 2) {
            $title = 'معاملات ورقيه';
            $data = paperTransaction::leftJoin('points', 'points.id', 'paper_transactions.point_id')->leftJoin('inspector_mission', 'inspector_mission.id', 'paper_transactions.mission_id')->where('paper_transactions.id', $id)->first();
            $details = [];
            $details_wepon=  [];

        } elseif ($type == 3) {
            $title = 'تفتيش';
            $data = PointContent::leftJoin('points', 'points.id', 'point_contents.point_id')->leftJoin('inspector_mission', 'inspector_mission.id', 'point_contents.mission_id')->where('point_contents.id', $id)->first();
            $details = DungeonInfo::where('content_id', $id)->get();
            $details_wepon= WeaponInfo::where('content_id', $id)->get();
        }else { //Violation
            $title = "سجل مخالفات";
            $data = Violation::leftJoin('grades', 'grades.id', 'violations.grade')->SelectRaw("image, violations.id,CONCAT_WS('/',violations.name,grades.name)  as name,(Select GROUP_CONCAT(violation_type.`name`) from violation_type where FIND_IN_SET(violation_type.id,violations.violation_type)) AS ViolationType,IF(violations.flag=1,'مخالفة سلوك انظباطي','مخالفة مباني') as Type")->leftJoin('inspector_mission', 'inspector_mission.id', 'violations.mission_id')->where('violations.id', $id)->first();
            $details = [];
            $details_wepon=  [];
        }
        return view('violations.details', compact('type', 'id', 'title', 'data', 'details','details_wepon'));
    }
}
