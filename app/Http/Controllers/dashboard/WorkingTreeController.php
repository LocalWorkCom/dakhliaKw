<?php

namespace App\Http\Controllers\dashboard;

use App\DataTables\IoTelegramDataTable;
use App\Http\Controllers\Controller;
use App\Models\WorkingTree;
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
        dd($request);
    }
    public function edit($id)
    {
        $WorkingTimes = WorkingTime::all();
        $WorkingTime = WorkingTree::with('WorkingTreeTime')->where($id)->first();
        return view('workingTree.edit', compact('WorkingTimes', 'WorkingTime'));
    }
    public function update(Request $request)
    {
        dd($request);
    }
    public function show($id)
    {
        $WorkingTree = WorkingTree::find($id);

        return view('workingTree.show', compact('WorkingTree'));
    }
}
