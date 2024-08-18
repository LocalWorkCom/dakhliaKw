<?php

namespace App\Http\Controllers;

use App\Models\AbsenceType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class AbsenceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('absence.index');
    }

    public function getAbsence()
    {
        $data = AbsenceType::all();

        return DataTables::of($data)->addColumn('action', function ($row) {

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
        ];
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|unique:absence_types,name',
        ], $messages);
        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try {
            $new = new AbsenceType();
            $new->name = $request->name;
            $new->save();
            return view('absence.index');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AbsenceType $absenceType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = AbsenceType::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $messages = [
            'name_edit.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
        ];
        $validatedData = Validator::make($request->all(), [
            'name_edit' => 'required',
        ], $messages);
        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        try {
            $new = AbsenceType::findOrFail($request->id_edit);
            $new->name = $request->name_edit;
            $new->save();
            // Dynamically create model instance based on the model class string
            return view('absence.index')->with('success', 'Absence created successfully.');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AbsenceType $absenceType)
    {
        //
    }
}
