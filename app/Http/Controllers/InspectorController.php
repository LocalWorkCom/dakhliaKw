<?php

namespace App\Http\Controllers;
use App\Models\Inspector;
use App\Models\User;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $inspectors = Inspector::all();
        $assignedInspectors = Inspector::whereNotNull('group_id')->count();
    $unassignedInspectors = Inspector::whereNull('group_id')->count();
        return view('inspectors.index' , compact('assignedInspectors', 'unassignedInspectors'));
    }
    public function addToGroup(Request $request)
    {
       $inspuctor = Inspector::findOrFail($request->id);
       $inspuctor->group_id = $request->group_id;
       $inspuctor->save();
       return redirect()->route('inspectors.index')->with('success', 'Inspector created successfully.')->with('showModal', true);
    }

    public function getInspectors()
    {
        $userDepartmentId = Auth::user()->department_id;
        $data = Inspector::with('user')
        ->whereHas('user', function ($query) use ($userDepartmentId) {
            $query->where('department_id', $userDepartmentId);
        })
        ->orderBy('id', 'desc')
        ->get();
       
        return DataTables::of($data)
        ->addColumn('group_id', function ($row) {
           return $row->group_id ? $row->group->name : 'لا يوجد مجموعه للمفتش';
        })
        ->addColumn('action', function ($row) {
            return '<button class="btn btn-primary btn-sm">Edit</button>';
        })
        
        ->rawColumns(['action'])
        ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
        $departmentId=auth()->user()->department_id;
        $inspectorUserIds = Inspector::pluck('user_id')->toArray();

        $users = User::where('flag', 'employee')
            ->where('department_id', $departmentId)
            ->whereNotIn('id', $inspectorUserIds)
            ->get();
         return view('inspectors.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        
        $request->validate([
            
        ]);
        $user=User::findOrFail($request->user_id);
         $inspector =new Inspector();
         $inspector->name=$request->name;
         $inspector->phone=$request->phone;

         $inspector->type=$request->type;

         $inspector->position=$request->position;

         $inspector->user_id =$request->user_id;
         $inspector->Id_number=$user->Civil_number;


          $inspector->save();

        //   dd($departements);
        return redirect()->route('inspectors.index')->with('success', 'Inspector created successfully.')->with('showModal', true);
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $inspector = Inspector::findOrFail($id);
        $users = User::get();
        return view('inspectors.show', compact('inspector','users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $inspector = Inspector::find($id);
        // dd($inspector);
        $departmentId = Auth::user()->department_id ? Auth::user()->department_id :null;
        $users = User::where('id', $inspector->id)->with('grade')->get();

        return view('inspectors.edit', compact('inspector','users'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        
        $request->validate([
            
        ]);
        $inspector = Inspector::find($id);
        $inspector->update($request->only(['position', 'name', 'phone', 'type']));
        // $inspector->save();
        // dd($inspector->id);
        return redirect()->route('inspectors.index')
                         ->with('success', 'Inspector updated successfully.')->with('showModal', true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
