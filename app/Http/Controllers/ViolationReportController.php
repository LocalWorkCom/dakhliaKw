<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\GroupTeam;
use Illuminate\Http\Request;

class ViolationReportController extends Controller
{
    //
    public function getdata()
    {
        $group = Groups::with('groupTeamsRelation')->get();
        // dd($group);

        return view('ReportViolation.index',compact('group'));
    }
}
