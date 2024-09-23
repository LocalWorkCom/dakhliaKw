<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Groups;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\paperTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class paperTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = Groups::all();
        $inspectors_group = Inspector::where('department_id', Auth()->user()->department_id)
            ->select('group_id')
            ->groupBy('group_id')
            ->pluck('group_id');

        if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $groups = Groups::all();
            $groupTeams = GroupTeam::all();
            $inspectors = Inspector::all();
        } else {
            $groups = Groups::whereIn('id', $inspectors_group)->get();
            $groupTeams = GroupTeam::whereIn('group_id', $inspectors_group)->get();
            $inspectors = Inspector::where('department_id', Auth()->user()->department_id)->get();
        }

        // Check if the request has a group or assign a default value (-1 means "All groups")
        $group = $request->input('group', '-1');

        // Date check as before
        $date = $request->input('date', date('Y-m-d'));

        return view('paperTransactions.index', compact('groups', 'groupTeams', 'inspectors', 'date', 'group'));
    }


    public function gettransactions(Request $request)
    {
        $data = PaperTransaction::with(['inspector', 'groupTeam', 'point']);

        if ($request->date && $request->date != '-1') {
            $data->where('date', $request->date);
        }

        if ($request->group && $request->group != '-1') {
            $inspectors = GroupTeam::where('group_id', $request->group)->pluck('inspector_ids')->flatten();
            $data->whereIn('inspector_id', $inspectors);
        }

        if ($request->team && $request->team != '-1') {
            $inspectors = GroupTeam::where('id', $request->team)->pluck('inspector_ids')->flatten();
            $data->whereIn('inspector_id', $inspectors);
        }

        if ($request->inspector && $request->inspector != '-1') {
            $data->where('inspector_id', $request->inspector);
        }

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary">View</button>';
            })
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
