<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class jobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("jobs.index");
    }
    public function getAllJob()
    {
        $data = job::get();
       
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<a class="btn btn-primary btn-sm" href=' . route('jobs.edit', $row->id) . '>تعديل</a>
            <a class="btn btn-primary btn-sm" href=' . route('jobs.show', $row->id) . '>التفاصيل</a>
            <a class="btn btn-primary btn-sm"  onclick="opendelete('.$row->id.')">حذف</a>' ;
        })
        ->rawColumns(['action'])
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
        $requestinput=$request->except('_token');
        $job = job::create($requestinput);
        $activeTab=2;
        $message="تم اضافة الوظيفه";
        return redirect()->route('job.index',compact('activeTab','message'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = job::findOrFail($id);
        return view("jobs.show" ,compact("data"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
         $data = job::findOrFail($id);
        return view("jobs.edit" ,compact("data"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $job = job::find($request->id);
        
        if (!$job) {
            return response()->json(['error' => 'Grade not found'], 404);
        }
        $job->name=$request->name;
        $job->save();
        $message='';
        return redirect()->route('job.index',compact('message'));
    }
    public function deletejob(Request $request )
    {
        dd($request->id);
        $isForeignKeyUsed = DB::table('users')->where('job_id', $request->id)->exists();
        //dd($isForeignKeyUsed);  
        if( $isForeignKeyUsed ){
            $message='';
            
        }else{
            $type= job::find($request->id);
            $type->delete();
            $message='';

        }
        return redirect()->route('job.index',compact('message'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
