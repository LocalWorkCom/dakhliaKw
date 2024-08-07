<?php

namespace App\Http\Controllers;

use App\Models\departements;
use App\Models\GroupTeam;
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
        //dd($request);
        // Find all teams that contain this inspector
        $teams = GroupTeam::where('group_id', $request->group_id)
                          ->whereJsonContains('inspector_ids', $request->id)
                          ->get();
                          
        foreach ($teams as $team) {
            // Decode the JSON field to an array
            $inspectorIds = json_decode($team->inspector_ids, true);
    
            // Remove the inspector from the array
            if (($key = array_search($request->id, $inspectorIds)) !== false) {
                unset($inspectorIds[$key]);
            }
    
            // Re-encode the array to JSON and save the team
            $team->inspector_ids = json_encode(array_values($inspectorIds));
            $team->save();
        }
    
        // Update the inspector's group
        $inspector = Inspector::findOrFail($request->id);
    
        $inspector->group_id = $request->group_id;
        $inspector->save();
        
        return redirect()->route('inspectors.index')
                         ->with('success', 'Inspector updated successfully.')
                         ->with('showModal', true);
        // $inspuctor = Inspector::findOrFail($request->id);
        // $inspuctor->group_id = $request->group_id;
        // $inspuctor->save();
        // return redirect()->route('inspectors.index')->with('success', 'Inspector created successfully.')->with('showModal', true);
 
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
            
            $name ="$row->name";
            $typesJson = json_encode($row->type_id); // Ensure this is an array

            // if($row->group_id !=  null){
            //     $edit_permission = '<a class="btn btn-sm" id="updateValueButton" style="background-color: #7e7d7c;" data-bs-toggle="modal" 
            //                data-bs-target="#myModal1"   openAddModal('.$row->id.' , '.$row->group_id.')> <i class="fa fa-edit"></i> تعديل مجموعه</a>';

            // }else{
            //     $edit_permission = '<a class="btn btn-sm" style="background-color: #F7AF15;" openAddModal('.$row->id.',0) data-bs-toggle="modal" 
            //                data-bs-target="#myModal1"> <i class="fa fa-plus"></i> أضافه</a>';

            // }
 
            // $edit_permission = null;
            // $show_permission = null ;
            // if (Auth::user()->hasPermission('edit item')) {
                // }
            // if (Auth::user()->hasPermission('view item')) {
            // }
        //     return '<a href="'.route('inspectors.edit',$row->id).'" class="btn btn-sm"  style="background-color: #F7AF15;">
        //     <i class="fa fa-edit"></i> تعديل 
        // </a>
        // <a href="'.route('inspectors.show',$row->id).'" class="btn btn-sm " style="background-color: #274373;">
        //    <i class="fa fa-eye"></i>عرض</a>'. $edit_permission;
        })
        
        ->rawColumns(['action'])
        ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $departmentId = Auth::user()->department_id;

         $department = departements::find($departmentId);
        $departmentId=auth()->user()->department_id;
        $inspectorUserIds = Inspector::pluck('user_id')->toArray();

        $users = User::where('flag', 'employee')
            ->where('department_id', $departmentId)
            ->where('id', '!=', $department->manger) 
            ->where('id', '!=', auth()->user()->id) 

            ->whereNotIn('id', $inspectorUserIds)
            ->get();
           //dd($users);
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
