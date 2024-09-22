<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\paperTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class paperTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $messages = [
            'point_id.required' => 'يجب اختيار النقطه المضاف لها المهمه',
        ];

        $validatedData = Validator::make($request->all(), [
            'point_id' => ['required'],
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->toDateString();
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        $teamName = GroupTeam::whereRaw('find_in_set(?, inspector_ids)', [$inspectorId])->value(column: 'name');

        $records = paperTransaction::where('status', 1)->where('point_id', $request->point_id)->where('inspector_id', $inspectorId)->where('date',$today)->get();
        foreach ($records as $record) {
            $all[] = [
                'id' => $record->id,
                'point_id' => $record->point_id,
                'point_name' => $record->point->name,
                'inspector_id' => $record->inspector_id,
                'inspector_name' => $record->inspector->name,
                'team_name' => $teamName,
                'mission_id' => $record->mission_id,
                'civil_number' => $record->civil_number,
                'registration_number' => $record->registration_number,
                'images' => $record->images,
                'created_at' => $record->created_at
            ];
        }
        $success['report'] = $all;


        if ($records) {
            return $this->respondSuccess($success, 'Data get successfully.');
        } else {

            return $this->apiResponse(true, 'Data get successfully.', null, 200);
        }
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
            'point_id.required' => 'يجب اختيار النقطه المضاف لها المهمه',
            'point_id.exists' => 'عفوا هذه النقطه غير متاحه',
            'registration_number.required' => 'يجب ادخال رقم القيد',
            'civil_number.required' => 'يجب ادخال رقم الأحوال',
            'mission_id.required' => 'يجب ادخال رقم المهمه',
        ];

        $validatedData = Validator::make($request->all(), [
            'point_id' => ['required'],
            'mission_id' => ['required'],
            'civil_number' => ['required'],
            'date' => ['required'],
            'registration_number' => ['required'],
        ], $messages);

        if ($validatedData->fails()) {
            return $this->respondError('Validation Error.', $validatedData->errors(), 400);
        }
        $today = Carbon::today()->toDateString();
        $inspectorId = Inspector::where('user_id', auth()->user()->id)->value('id');
        // shift
        $inspector = InspectorMission::where('inspector_id', $inspectorId)->where('date', $today)->where('day_off', 0)->first();
        if ($request->id) {
            $record = paperTransaction::where('id', $request->id)->first();
            $isparent = $record->parent;
            if ($isparent == 0) {
                $record->status = 0;
                $record->save();
                $new = new paperTransaction();
                $new->point_id = $request->point_id;
                $new->mission_id = $request->mission_id;
                $new->inspector_id = $inspectorId;
                $new->civil_number = $request->civil_number;
                $new->date = $request->date;

                $new->registration_number = $request->registration_number;
                $new->status = 1;
                $new->parent = $request->id;
                $new->created_by = auth()->user()->id;
                $new->save();
                if ($request->hasFile('images')) {
                    $files = $request->file('images');
                    $path = 'Api/images/paperTransactions';
                    $model = paperTransaction::find($new->id);
                    UploadFilesIM($path, 'images', $model, $files);
                }
                $recor = paperTransaction::find($new->id);
                $success['report'] = $recor->only('id', 'point_id', 'mission_id', 'inspector_id', 'civil_number', 'registration_number', 'images', 'created_at');


                if ($new) {
                    return $this->respondSuccess($success, 'Data get successfully.');
                } else {

                    return $this->apiResponse(true, 'Data get successfully.', null, 200);
                }
            } else {
                $records = paperTransaction::where('parent', $isparent)->pluck('id')->toArray();
                foreach ($records as $record) {
                    $recs = paperTransaction::find($record);
                    $recs->status = 0;
                    $recs->save();
                }
                $new = new paperTransaction();
                $new->point_id = $request->point_id;
                $new->mission_id = $request->mission_id;
                $new->inspector_id = $inspectorId;
                $new->civil_number = $request->civil_number;
                $new->date = $request->date;

                $new->registration_number = $request->registration_number;
                $new->status = 1;
                $new->parent = $isparent;
                $new->created_by = auth()->user()->id;
                $new->save();
                if ($request->hasFile('images')) {
                    $files = $request->file('images');
                    $path = 'Api/images/paperTransactions';
                    $model = paperTransaction::find($new->id);
                    UploadFilesIM($path, 'images', $model, $files);
                }
                $recor = paperTransaction::find($new->id);
                $success['report'] = $recor->only('id', 'point_id', 'mission_id', 'inspector_id', 'civil_number', 'registration_number', 'images', 'created_at');


                if ($new) {
                    return $this->respondSuccess($success, 'Data get successfully.');
                } else {

                    return $this->apiResponse(true, 'Data get successfully.', null, 200);
                }
            }
        } else {
            $new = new paperTransaction();
            $new->point_id = $request->point_id;
            $new->mission_id = $request->mission_id;
            $new->inspector_id = $inspectorId;
            $new->civil_number = $request->civil_number;
            $new->date = $request->date;

            $new->registration_number = $request->registration_number;
            $new->status = 1;
            $new->parent = 0;
            $new->created_by = auth()->user()->id;
            $new->save();
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $path = 'Api/images/paperTransactions';
                $model = paperTransaction::find($new->id);
                UploadFilesIM($path, 'images', $model, $files);
            }
            $record = paperTransaction::find($new->id);
            $success['report'] = $record->only('id', 'point_id', 'mission_id', 'inspector_id', 'civil_number', 'registration_number', 'images', 'created_at');


            if ($new) {
                return $this->respondSuccess($success, 'Data get successfully.');
            } else {

                return $this->apiResponse(true, 'Data get successfully.', null, 200);
            }
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
    public function update(Request $request, string $id)
    {
        //
    }
    function UploadFilesIM($path, $inputName, $model, $files)
    {
        $uploadedImages = [];

        foreach ($files as $file) {
            // Generate a unique filename
            $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move the file to the specified path
            $file->move(public_path($path), $filename);

            // Store the file URL
            $uploadedImages[] = url($path . '/' . $filename);
        }

        return $uploadedImages;
        function UploadFilesIM($path, $inputName, $model, $files)
        {
            $uploadedImages = [];

            foreach ($files as $file) {
                // Generate a unique filename
                $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Move the file to the specified path
                $file->move(public_path($path), $filename);

                // Store the file URL
                $uploadedImages[] = url($path . '/' . $filename);
            }

            return $uploadedImages;
        }
        // Handle image upload and merging old and new images
        $images = $request->old_images ?? [];  // Get old images from request
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $path = 'Api/images/paperTransactions';
            $model = paperTransaction::find($new->id);
            $newImages = $this->UploadFilesIM($path, 'images', $model, $files);
            $images = array_merge($images, $newImages); // Combine old and new images
        }
        $new->images = json_encode($images); // Save merged images
        $new->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}