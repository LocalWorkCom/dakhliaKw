<?php

namespace App\Http\Controllers;

use App\Models\ForceName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class forceNamesController extends Controller
{
    public function index()
    {
        return view('forceNames.index');
    }
    public function getAllNames()
    {
        $data = ForceName::orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)->addColumn('action', function ($row) {
            $name = "'$row->name'";
            $edit_permission = null;
            // if (Auth::user()->hasPermission('edit job')) {
            $edit_permission = '<a class="btn btn-sm"  style="background-color: #F7AF15;"  onclick="openedit(' . $row->id . ',' . $name . ')">  <i class="fa fa-edit"></i> تعديل </a>';
            // }

            $uploadButton = $edit_permission;
            return $uploadButton;
        })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $rules = [
            'nameadd' => 'required|string',
        ];

        $messages = [
            'nameadd.required' => 'يجب ادخال المسمى ',

        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }

        $new =new ForceName();
        $new->name = $request->nameadd;
        $new->save();

        return redirect()->back()->with('message','تم أضافه بنجاح');
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|string',
        ];

        $messages = [
            'name.required' => 'يجب ادخال المسمى ',

        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()]);
        }

        $new =ForceName::findOrFail($request->id);
        $new->name = $request->name;
        $new->save();

        return redirect()->back()->with('message','تم أضافه بنجاح');
    }
}
