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
            'pointsIDs' => 'required|array|exists:group_points,id',
        ];

        $messages = [
            'name.required' => 'يجب ادخال اسم القطاع',
            'governorate.required' => 'يجب اختيار محافظة',
            'pointsIDs.required' => 'يجب اختيار نقطة واحدة على الأقل',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $sector_id = Sector::whereJsonContains('governments_IDs', $request->governorate)->value('id');
        $pointsids = Grouppoint::whereIn('id', $request->pointsIDs)->pluck('points_ids')->toArray();
        //dd($pointsids );
        $ids = array_merge(...$pointsids);
        $points = new Grouppoint();
        $points->name = $request->name;
        $points->points_ids = $ids;
        $points->government_id  = $request->governorate;
        $points->sector_id  = $sector_id;
        $points->flag  = 1;
        $points->save();
        //dd($request->pointsIDs);
        $pointsIDs = is_array($request->pointsIDs) ? $request->pointsIDs : json_decode($request->pointsIDs, true);

        $deleted = Grouppoint::where('flag', 0)
            ->whereIn('id', $request->pointsIDs)
            ->get();

        // Optional: Perform actions with the retrieved records
        foreach ($deleted as $record) {
            // Example action: Delete the record
            $record->deleted = 1;
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

        // Decode points_ids if it is a JSON string; otherwise, use it as is
        $pointsIds = is_array($data->points_ids) ? $data->points_ids : json_decode($data->points_ids, true);
        $selectedPoints = Point::whereIn('id', $pointsIds)->get();

        // Get unique government IDs from the selected points
        $governmentIds = $selectedPoints->pluck('government_id')->unique();

        // Fetch all points for the same government
        $allPoints = Point::whereIn('government_id', $governmentIds)->get();
        $groupPoints = GroupPoint::where('government_id', 4)
        ->where('flag', 0)
        ->where('deleted', 0)
        ->get();
        // Get all points in the Grouppoint table that belong to the same government(s)
        // $pointsInGroup = Grouppoint::whereIn('government_id', $governmentIds)
        //     ->pluck('points_ids')->where('deleted', 0)->where('flag', 0)
        //     ->map(function ($item) {
        //         return is_array($item) ? $item : json_decode($item, true); // Convert JSON/serialized data to array
        //     })
        //     ->flatten()
        //     ->unique();
        //     dd($pointsInGroup);
        // // Filter points to include only those that are not already in another group
        // $availablePoints = $allPoints->filter(function ($point) use ($pointsInGroup) {
        //     return $pointsInGroup->contains($point->id);
        // });
        // Collect IDs from available points
        $availablePointIds = $groupPoints->pluck('points_ids')->unique();
       // dd($data ,$pointsIds,$selectedPoints,$availablePoints,$availablePointIds);

        // Merge selected points with available points
        $mergedPointIds = $selectedPoints->pluck('id')->merge($availablePointIds);

        // Fetch the final set of points by their IDs
        $finalPoints = Point::whereIn('id', $mergedPointIds)->get();
        //dd($finalPoints);

        return view('grouppoints.edit', [
            'data' => $data,
            'selectedPoints' => $finalPoints
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
            $record->deleted = 1;
            $record->save();
        }
        // Add removed values to Grouppoint with flag = 0
        foreach ($removedPointsIDs as $removedID) {
            $idpoint = Grouppoint::where('flag', 0)
                ->whereJsonContains('points_ids', $removedID)
                ->first();
            //dd($idpoint);
            $idpoint->deleted = 0;
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
