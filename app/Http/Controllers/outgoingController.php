<?php

namespace App\Http\Controllers;

use App\DataTables\outgoingsDataTable;
use App\Http\Controllers\Controller;
use App\Models\ExternalDepartment;
use App\Models\outgoing_files;
use App\Models\outgoings;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class outgoingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(outgoingsDataTable $dataTable)
    {
       
        return $dataTable->render('outgoing.viewAll');
       //return view("outgoing.viewAll");
   
    }
    public function uploadFiles($id){
        dd($id);
    }
    public function showFiles($id){
        dd($id);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
        $users=User::all();
        $departments=ExternalDepartment::all();
        return view('outgoing.add', compact('users','departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      dd($request->all());
        $export = new outgoings();
        $export->name = $request->name;
        $export->num = $request->num;
        $export->note = $request->note;
        $export->person_to = $request->person_to;
        $export->created_by = auth()->id;
        $export->active = $request->active;
        $export->updated_by = $request->active;
        $export->department_id = $request->department;

        $export->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=User::all();
        $is_file = outgoing_files::where('outgoing_id', $id)->exists();
        return view('outgoing.show', compact('data','users','is_file'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data=outgoings::with(['personTo', 'createdBy', 'updatedBy'])->findOrFail($id);
        $users=User::all();
        $is_file = outgoing_files::where('outgoing_id', $id)->exists();
        return view('outgoing.edit', compact('data','users','is_file'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
