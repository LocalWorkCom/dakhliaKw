<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\outgoings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\departements;
use Yajra\DataTables\DataTables;
use App\DataTables\DepartmentDataTable;

class SearchController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request, $search, $q = "")
    {

        $mode = 'search';
        $search = $request->search;
        $parentDepartment = Departements::find(Auth()->user()->department_id);

        if ($search == 'users') {
            $id = '0';

            return view('user.view', compact('id', 'mode', 'q', 'search'));
        }
        if ($search == 'emps') {
            $id = '1';

            return view('user.view', compact('id', 'mode', 'q', 'search'));
        }
        if ($search == 'dept') {

            $users = User::where('flag', 'employee')->where('department_id', NULL)->get();
            $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();

            // Get the children of the parent department 
            $departments = $parentDepartment ? $parentDepartment->children : collect();
            if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
                $subdepartments = departements::with('children')->get();
            } else {
                $subdepartments = departements::where('id', Auth::user()->department_id)->with('children')->get();
            }

            return view('departments.index', compact('users', 'subdepartments', 'departments', 'parentDepartment', 'q', 'search', 'mode'));
        }
    }
    public function getUsers(Request $request, $id = 0, $q = "")
    {

        $parentDepartment = (object)[];
        if (is_null(Auth()->user()->department_id)) {
            $parentDepartment = null;
            $dep_id = null;
        } else {

            $dep_id = Auth()->user()->department_id;
            $parentDepartment = Departements::find($dep_id);
        }

        if (!is_null($parentDepartment) && is_null($parentDepartment->parent_id)) {
            if (auth()->user()->rule_id == 2) {
                $subdepart = null;
            } else {

                $subdepart = Departements::where('parent_id', $parentDepartment->id)->pluck('id')->toArray();
            }

            $userData = User::where('flag', (!$id) ? 'user' : 'employee')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%')
                        ->orWhere('email', 'LIKE', '%' . $q . '%')
                        ->orWhere('phone', 'LIKE', '%' . $q . '%')
                        ->orWhere('military_number', 'LIKE', '%' . $q . '%')
                        ->orWhere('Civil_number', 'LIKE', '%' . $q . '%')
                        ->orWhere('file_number', 'LIKE', '%' . $q . '%');
                })->where(function ($query) use ($subdepart, $parentDepartment) {
                    $query->whereIn('department_id', $subdepart)
                        ->orWhere('department_id', $parentDepartment->id);
                })->get();
        } else {
            $userData = User::where('flag', (!$id) ?  'user' : 'employee')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%')
                        ->orWhere('email', 'LIKE', '%' . $q . '%')
                        ->orWhere('phone', 'LIKE', '%' . $q . '%')
                        ->orWhere('military_number', 'LIKE', '%' . $q . '%')
                        ->orWhere('Civil_number', 'LIKE', '%' . $q . '%')
                        ->orWhere('file_number', 'LIKE', '%' . $q . '%');
                });
            if (!is_null(Auth()->user()->department_id)) {
                $userData = $userData->where('department_id', $parentDepartment->id);
            }
            $userData = $userData->get();
        }
        return DataTables::of($userData)->addColumn('action', function ($row) {

            return $row;
        })
            ->addColumn('department', function ($row) { // New column for departments count

                $department = departements::where('id', $row->department_id)->pluck('name')->first();
                return $department;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getDepartments(Request $request, $q = "")
    {
        //  dd($q);
        /*   if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
            $depdata = departements::withCount('iotelegrams')
        
                ->withCount('outgoings')
                ->withCount('children')
                ->with(['children'])
                //->where('name','LIKE','%'.$q.'%')
                ->orderBy('id', 'desc')->get();
        } else { */
        $depdata = departements::withCount('iotelegrams')
            ->withCount('outgoings')
            ->withCount('children')
            ->with(['children'])
            ->where('name', 'LIKE', '%' . $q . '%')
            ->orderBy('id', 'desc');
        if (auth()->user()->rule_id != 2) {

            $depdata = $depdata->where('id', Auth::user()->department_id);
        }
        $depdata = $depdata->get();

        // }

        return DataTables::of($depdata)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-primary btn-sm">Edit</button>';
            })
            ->addColumn('iotelegrams_count', function ($row) {
                return $row->iotelegrams_count;  // Display the count of iotelegrams
            })
            ->addColumn('outgoings_count', function ($row) {
                return $row->outgoings_count;
            })
            ->addColumn('children_count', function ($row) { // New column for departments count
                return $row->children_count;
            })
            ->addColumn('manager_name', function ($row) {
                return $row->manager ? $row->manager->name : 'N/A'; // Display the manager's name
            })
            ->rawColumns(['action'])
            ->make(true);
    }
// old code
    // public function index(Request $request,$search,$q)
    // {
       
    //     $mode='search';
    //     $q=$request->q;
    //     $search=$request->search;
    //     $parentDepartment = Departements::find(Auth()->user()->department_id);

    //     if($search=='users')
    //     { $id='0';
           
    //         return view('user.view', compact('id','mode','q','search'));
    //     }
    //     if($search=='emps')
    //     {
    //         $id='1';
           
    //         return view('user.view', compact('id','mode','q','search'));
    //     }
    //     if($search=='dept')
    //     {
          
    //         $users = User::where('flag', 'employee')->where('department_id', NULL)->get();
    //         $parentDepartment = departements::where('parent_id', Auth::user()->department_id)->first();
    
    //         // Get the children of the parent department 
    //         $departments = $parentDepartment ? $parentDepartment->children : collect();
    //         if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
    //             $subdepartments = departements::with('children')->get();
    //         } else {
    //             $subdepartments = departements::where('id', Auth::user()->department_id)->with('children')->get();
    //         }
    
    //         return view('departments.index', compact('users', 'subdepartments', 'departments', 'parentDepartment','q','search','mode'));
    //     }
      
    // }
    // public function getUsers(Request $request,$q,$id)
    // {
       
    //     $parentDepartment = Departements::find(Auth()->user()->department_id);
 
    //     if (is_null($parentDepartment->parent_id)) {
    //         $subdepart = Departements::where('parent_id', $parentDepartment->id)->pluck('id')->toArray();
    //         $userData=User::where('type','users')->where('name','LIKE','%'.$q.'%')->orWhere('email','LIKE','%'.$q.'%')->orWhere('phone','LIKE','%'.$q.'%')->orWhere('military_number','LIKE','%'.$q.'%')->orWhere('Civil_number','LIKE','%'.$q.'%')->orWhere('file_number','LIKE','%'.$q.'%')->where(function ($query) use ($subdepart, $parentDepartment) {
    //             $query->whereIn('department_id', $subdepart)
    //                 ->orWhere('department_id', $parentDepartment->id);
    //         })->get();
    //     }else{
    //         $userData=User::where('type','users')->where('name','LIKE','%'.$q.'%')->orWhere('email','LIKE','%'.$q.'%')->orWhere('phone','LIKE','%'.$q.'%')->orWhere('military_number','LIKE','%'.$q.'%')->orWhere('Civil_number','LIKE','%'.$q.'%')->orWhere('file_number','LIKE','%'.$q.'%')  ->where('department_id', $parentDepartment->id)->get();
    //     }
    //       //  dd($userData);
    //         return DataTables::of($userData)->addColumn('action', function ($row) {

    //             return $row;
    //         })
    //             ->addColumn('department', function ($row) { // New column for departments count

    //                 $department = departements::where('id', $row->department_id)->pluck('name')->first();
    //                 return $department;
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
            
    // }

    // public function getDepartments(Request $request,$q)
    // {
    //   //  dd($q);
    //   /*   if (Auth::user()->rule->name == "localworkadmin" || Auth::user()->rule->name == "superadmin") {
    //         $depdata = departements::withCount('iotelegrams')
        
    //             ->withCount('outgoings')
    //             ->withCount('children')
    //             ->with(['children'])
    //             //->where('name','LIKE','%'.$q.'%')
    //             ->orderBy('id', 'desc')->get();
    //     } else { */
    //         $depdata = departements::withCount('iotelegrams')
           
    //             ->withCount('outgoings')
    //             ->withCount('children')
    //             ->where('id', Auth::user()->department_id)
    //             ->with(['children'])
    //            // ->where('name','LIKE','%'.$q.'%')
    //             ->orderBy('id', 'desc')->get();
    //    // }

    //     //dd($depdata);
    //     return DataTables::of($depdata)
    //         ->addColumn('action', function ($row) {
    //             return '<button class="btn btn-primary btn-sm">Edit</button>';
    //         })
    //         ->addColumn('iotelegrams_count', function ($row) {
    //             return $row->iotelegrams_count;  // Display the count of iotelegrams
    //         })
    //         ->addColumn('outgoings_count', function ($row) {
    //             return $row->outgoings_count;
    //         })
    //         ->addColumn('children_count', function ($row) { // New column for departments count
    //             return $row->children_count;
    //         })
    //         ->addColumn('manager_name', function ($row) {
    //             return $row->manager ? $row->manager->name : 'N/A'; // Display the manager's name
    //         })
    //         ->rawColumns(['action'])
    //         ->make(true);
    // }

}
