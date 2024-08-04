<?php

namespace App\Http\Controllers\dashboard;

use App\DataTables\IoTelegramDataTable;
use App\Http\Controllers\Controller;
use App\Models\WorkingTime;
use App\Models\WorkingTree;
use App\Models\WorkingTreeTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WorkingTreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('workingTree.index');
    }

    public function getWorkingTrees()
    {
        $WorkingTrees = WorkingTree::orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($WorkingTrees)
            ->rawColumns(['action'])
            ->make(true);
    }
    public function create()
    {
        $WorkingTimes = WorkingTime::all();
        return view('workingTree.add', compact('WorkingTimes'));
    }
    public function store(Request $request)
    {
        $WorkingTree = new WorkingTree;
        $WorkingTree->name = $request->name;
        $WorkingTree->working_days_num = $request->working_days_num;
        $WorkingTree->holiday_days_num = $request->holiday_days_num;
        $WorkingTree->created_by = auth()->id();
        $WorkingTree->created_departement = auth()->user()->department_id;
        $WorkingTree->save();
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holiday = "holiday" . $i;
            $WorkingTreeTime = new WorkingTreeTime;
            $WorkingTreeTime->working_time_id = $request->$holiday;
            $WorkingTreeTime->working_tree_id = $WorkingTree->id;
            $WorkingTreeTime->day_num = $i;
            $WorkingTreeTime->created_by = auth()->id();
            $WorkingTreeTime->created_departement = auth()->user()->department_id;
            $WorkingTreeTime->save();
        }
        session()->flash('success', 'تم الحفظ بنجاح.');

        return redirect()->route('working_trees.list');
    }
    public function edit($id)
    {
        $workingTimes = WorkingTime::all();
        $workingTree = WorkingTree::with('workingTreeTimes')->where('id', $id)->first();
        return view('workingTree.edit', compact('workingTimes', 'workingTree'));
    }
    public function update(Request $request, $id)
    {
        $WorkingTree =  WorkingTree::find($id);
        $WorkingTree->name = $request->name;
        $WorkingTree->working_days_num = $request->working_days_num;
        $WorkingTree->holiday_days_num = $request->holiday_days_num;
        // $WorkingTree->created_by = auth()->id();
        // $WorkingTree->created_departement = auth()->user()->department_id;
        $WorkingTree->save();
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holiday = "holiday" . $i;

            $checkTimeExist = WorkingTreeTime::where('day_num', $i)->where('working_tree_id', $id)->first();
            if (!$checkTimeExist) {
                $WorkingTreeTime = new WorkingTreeTime;
                $WorkingTreeTime->working_time_id = $request->$holiday;
                $WorkingTreeTime->working_tree_id = $id;
                $WorkingTreeTime->day_num = $i;
                $WorkingTreeTime->created_by = auth()->id();
                $WorkingTreeTime->created_departement = auth()->user()->department_id;
                $WorkingTreeTime->save();
            }else{
                $checkTimeExist->working_time_id = $request->$holiday;
                $checkTimeExist->working_tree_id = $id;
                $checkTimeExist->day_num = $i;
                $checkTimeExist->created_by = auth()->id();
                $checkTimeExist->created_departement = auth()->user()->department_id;
                $checkTimeExist->save();
            }
        }
        session()->flash('success', 'تم التعديل بنجاح.');

        return redirect()->route('working_trees.list');
    }
    public function show($id)
    {
        $WorkingTree = WorkingTree::find($id);

        return view('workingTree.show', compact('WorkingTree'));
    }
}
