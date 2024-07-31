<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class regionsController extends Controller
{

      //START government
    //show governments
    public function indexgovernment()
    {
    return view("governments.index");
    }
    //create governments
    public function creategovernment()
    {
        return view("governments.add");
    }

    //get data for governments
    public function getAllgovernment()
    {
        $data = Government::orderBy('updated_at','desc')->orderBy('created_at','desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            if(Auth::user()->hasPermission('edit Government')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit('.$row->id.','.$name.')">  <i class="fa fa-edit"></i> تعديل </a>';
            }
            // if(Auth::user()->hasPermission('view Region')){
                $region_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  href="'.route('regions.index').'">  <i class="fa fa-edit"></i> مناطق </a>';
            // }
            return $edit_permission .' '. $region_permission ;

            // <a class="btn btn-primary btn-sm" href=' . route('government.show', $row->id) . '>التفاصيل</a>
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    //add government
    public function addgovernment(Request $request){
       
        $requestinput=$request->except('_token');
        $job = new Government();
        $job->name=$request->nameadd;
        $job->save();
        $message="تم اضافه المحافظه";
        return redirect()->route('government.all',compact('message'));
        //return redirect()->back()->with(compact('activeTab','message'));
    }
    //show government
    public function showgovernment($id)
    {
        $data = Government::findOrFail($id);
        return view("governments.show" ,compact("data"));
    }
    //edit governments
    public function editgovernment($id)
    {
        $data = Government::findOrFail($id);
        return view("governments.edit" ,compact("data"));
    }
     //update governments
     public function updategovernment(Request $request)
     {
        $gover = Government::find($request->id);

        if (!$gover) {
            return response()->json(['error' => 'هذه الماحفظه غير موجوده'], 404);
        }
        $gover->name=$request->name;
        $gover->save();

        $message='';
        return redirect()->route('government.all',compact('message'));
     }

    //END government
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("regions.index");
    }
    public function getregions()
    {
        $data = Region::with('government')
        ->select('regions.*', 'governments.name as government_name')
        ->join('governments', 'regions.government_id', '=', 'governments.id')
        ->orderBy('regions.updated_at', 'desc')
        ->orderBy('regions.created_at', 'desc')
        ->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            // if(Auth::user()->hasPermission('edit Region')){
                $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit('.$row->id.','.$name.','.$row->government_id.')">  <i class="fa fa-edit"></i> تعديل </a>';
            // }
            return $edit_permission ;

            // <a class="btn btn-primary btn-sm" href=' . route('government.show', $row->id) . '>التفاصيل</a>
        })
        ->addColumn('government_name', function ($row) {
            return $row->government_name;
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
        $region = new Region();
        $region->name=$request->nameadd;
        $region->government_id=$request->government;
        $region->save();
        $message="تم اضافه المنطقه";
        return redirect()->route('regions.index',compact('message'));
    }

   
    public function update(Request $request)
    {
        $region = Region::find($request->id);

        //dd($request);
        if (!$region) {
            return response()->json(['error' => 'هذه المنطقه غير موجوده'], 404);
        }
        $region->name=$request->name;
        $region->government_id=$request->government;
        $region->save();

        $message='تم التعديل على المنطقه';
        return redirect()->route('regions.index',compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
       // $isForeignKeyUsed = DB::table('users')->where('qualification_id', $request->id)->exists();
        // //dd($isForeignKeyUsed);
        // if( $isForeignKeyUsed ){
        //     return redirect()->route('qualifications.index')->with(['message' => 'لا يمكن حذف هذا المؤهل  يوجد موظفين له']);
        // }else{
            $type= Region::find($request->id);
            $type->delete();
            return redirect()->route('qualifications.index')->with(['message' => 'تم حذف المنطقه']);

        // }
    }
}