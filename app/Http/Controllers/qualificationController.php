<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Qualifications;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Yajra\DataTables\Facades\DataTables;

class qualificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("qualifications.index");
    }

    public function getqualification(){
      
        $data = Qualifications::orderBy('updated_at','desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            return '<a class="btn  btn-sm" style="background-color: #259240;"  onclick="openedit('.$row->id.','.$name.')"> <i class="fa fa-edit"></i> </a>
            <a class="btn  btn-sm" style="background-color: #C91D1D;"  onclick="opendelete('.$row->id.')"> <i class="fa-solid fa-trash"></i> </a>' ;
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
        $rules = [
            'nameadd' => 'required|string',
        ];

        $messages = [
            'nameadd.required' => 'يجب ادخال اسم المؤهل',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);
        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
        if (auth()->id()) {
        $user=User::find(Auth::user()->id);
        $requestinput=$request->except('_token');
        $quali = new Qualifications();
          $quali->name=$request->nameadd;
          $quali->created_by=$user->id;
          $quali->save();
          $message="تم اضافه المؤهل";
          return redirect()->route('qualifications.index',compact('message'));
        }else{
            return redirect()->route('login');
        }
       
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
    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|string',
        ];

        $messages = [
            'name.required' => 'يجب ادخال اسم المؤهل',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);
        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }
        if (auth()->id()) {
        $user=User::find(Auth::user()->id);
        $requestinput=$request->except('_token');
        $quali = Qualifications::find($request->id);
          $quali->name=$request->name;
          $quali->created_by=$user->id;
          $quali->save();
          $message="تم تعديل المؤهل";
          return redirect()->route('qualifications.index',compact('message'));
        }else{
            return redirect()->route('login');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $isForeignKeyUsed = DB::table('users')->where('qualification_id', $request->id)->exists();
        //dd($isForeignKeyUsed);
        if( $isForeignKeyUsed ){
            return redirect()->route('qualifications.index')->with(['message' => 'لا يمكن حذف هذا المؤهل  يوجد موظفين له']);
        }else{
            $type= Qualifications::find($request->id);
            $type->delete();
            return redirect()->route('qualifications.index')->with(['message' => 'تم حذف المؤهل']);

        }
    }
}
