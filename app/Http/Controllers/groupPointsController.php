<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class GroupPointsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('grouppoints.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $rules = [
            'name' => 'required|string',
            'governorate' => 'required|exists:governments,id',
            'pointsIDs' => 'required|array|exists:points,id',

        ];

        // // Define custom messages
        $messages = [
            'name.required' => 'يجب ادخال اسم القطاع',
            'name.string' => 'يجب ان لا يحتوى اسم القطاع على رموز',
            'pointsIDs.required' => 'يجب اختيار نقطه واحده على الاقل',
            'pointsIDs.exists' => ''

        ];

        // // Validate the request
        $validatedData = Validator::make($request->all(), $rules, $messages);
       
        // // Validate the request
        // $request->validate($rules, $messages);
        if ($validatedData->fails()) {
          
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $points = new Grouppoint();
        $points->name = $request->name;
        $points->points_ids = $request->pointsIDs;
        $points->government_id  = $request->governorate;
        $points->save();
        return redirect()->route('points.index')->with('message', 'تم أضافه قطاع جديد');
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
         $data = Grouppoint::findOrFail($id);
         $selectedPoints = $data->points()->pluck('id');
        // // Retrieve all government IDs associated with any sector except the current sector
        // $associatedGovernmentIds = Sector::query()
        //     ->where('id', '!=', $data->id)
        //     ->pluck('governments_IDs')
        //     ->flatten()
        //     ->unique()
        //     ->toArray();
    
        // // Retrieve governments not associated with any sector
        // $unassociatedGovernments = Government::query()
        //     ->whereNotIn('id', $associatedGovernmentIds)
        //     ->get();
    
        // // Retrieve governments associated with the current sector
        // $currentSectorGovernments = Government::query()
        //     ->whereIn('id', $data->governments_IDs)
        //     ->get();
    
        // // Merge the current sector's governments with the unassociated governments
        // $governments = $currentSectorGovernments->merge($unassociatedGovernments);
    
        return view('grouppoints.edit',[
            'data' => $data,
            'selectedPoints'=>$selectedPoints 
        ]);
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
