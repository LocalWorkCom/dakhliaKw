<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Government;
use App\Models\Grouppoint;
use App\Models\Point;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class groupPointsController extends Controller
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
        $sector_id = Sector::whereJsonContains('governments_IDs',$request->governorate)->value('id');
       // dd($sector_id ,$request->governorate );
        $points = new Grouppoint();
        $points->name = $request->name;
        $points->points_ids = $request->pointsIDs;
        $points->government_id  = $request->governorate;
        $points->sector_id  = $sector_id ;
        $points->flag  = 1;

        $points->save();
        $pointsIDs = is_array($request->pointsIDs) ? $request->pointsIDs : json_decode($request->pointsIDs, true);

        $deleted = Grouppoint::where('flag', 0)
            ->where(function ($query) use ($pointsIDs) {
                foreach ($pointsIDs as $id) {
                    $query->orWhereRaw('JSON_CONTAINS(points_ids, ?) = 1', [json_encode($id)]);
                }
            })
            ->get();
            //dd( $deleted);
        // Optional: Perform actions with the retrieved records
        foreach ($deleted as $record) {
            // Example action: Delete the record
            $record->deleted=1;
            $record->save();
        }
        return redirect()->route('points.index')->with('message', 'تم أضافه مجموعه');
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
        // dd($data);
        // Fetch the selected points
        $selectedPoints = Point::whereIn('id', $data->points_ids)->get();

        // Get unique government IDs from the selected points
        $governmentIds = $selectedPoints->pluck('government_id')->unique();
       // dd($governmentIds);
        // Fetch all points for the same government
        $allPoints = Point::whereIn('government_id', $governmentIds)->get();

        // Get all points in the Grouppoint table that belong to the same government(s)
        $pointsInGroup = Grouppoint::whereIn('government_id', $governmentIds)
            ->pluck('points_ids')
            ->map(function ($item) {
                return is_array($item) ? $item : json_decode($item, true); // Convert JSON/serialized data to array
            })
            ->flatten()
            ->unique();

        // Filter points to include only those that are not already in another group
        $availablePoints = $allPoints->filter(function ($point) use ($pointsInGroup) {
            return !$pointsInGroup->contains($point->id);
        });
        $mergedPoints = $pointsInGroup->merge($availablePoints->pluck('id')->toArray());

        // Ensure uniqueness after merging
        $mergedPoints = $mergedPoints->unique();
        $points = Point::whereIn('id', $mergedPoints)->get();

        //dd($mergedPoints);

        //  $selectedPoints = $data->pluck('points_ids')->toArray();


        return view('grouppoints.edit', [
            'data' => $data,
            'selectedPoints' => $points
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $rules = [
            'name' => 'required|string',
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
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
       // dd($id);
        $points = Grouppoint::find($request->id);
        $oldPointsIDs = $points->points_ids;
        $points->name = $request->name;
        $points->points_ids = $request->pointsIDs;
        $points->government_id = $request->government_id;
        $points->flag = 1;
        $points->save();

        // Decode pointsIDs array
        $pointsIDs = is_array($request->pointsIDs) ? $request->pointsIDs : json_decode($request->pointsIDs, true);

        // Retrieve the old points_ids from the database
       

        // Find removed values
        $removedPointsIDs = array_diff($oldPointsIDs, $pointsIDs);

//dd($removedPointsIDs);

        // Handle deletion of records with flag = 0 and matching points_ids
        $deleted = Grouppoint::where('flag', 0)
            ->where(function ($query) use ($pointsIDs) {
                foreach ($pointsIDs as $id) {
                    $query->orWhereRaw('JSON_CONTAINS(points_ids, ?) = 1', [json_encode($id)]);
                }
            })
            ->get();

        foreach ($deleted as $record) {
            $record->deleted=1;
            $record->save();
        }
        // Add removed values to Grouppoint with flag = 0
        foreach ($removedPointsIDs as $removedID) {
           $idpoint= Grouppoint::where('flag', 0)
            ->whereJsonContains('points_ids', $removedID)
             ->first();
             //dd($idpoint);
          $idpoint->deleted=0;
          $idpoint->save();
        }
        return redirect()->route('points.index')->with('message', 'تم تعديل مجموعه ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
