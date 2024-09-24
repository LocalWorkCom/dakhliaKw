<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grouppoint;
use App\Models\GroupTeam;
use App\Models\Inspector;
use App\Models\InspectorMission;
use App\Models\paperTransaction;
use App\Models\PointDays;
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
        $all=[];
        $records = paperTransaction::where('status', 1)->where('point_id', $request->point_id)->where('inspector_id', $inspectorId)->where('date',$today)->get();
        foreach ($records as $record) {
            $pointShift = PointDays::where('point_id', $record->point_id)
            ->where('name', Carbon::parse($record->created_at)->dayOfWeek)
            ->first();

        if ($record->point_id) {

            $shiftDetails = [
                'start_time' => '00:00',
                'end_time' => '23:59',
                'time' => null
            ];

            // Override with actual shift if available
            if ($pointShift && $pointShift->from && $pointShift->to) {
                $shiftDetails = [
                    'start_time' => $pointShift->from,
                    'end_time' => $pointShift->to,
                    'time' => null // As per requirement
                ];
            }
        }
            $all[] = [
                'id' => $record->id,
                'governrate'=>$record->point->government->name,
                'point_id' => $record->point_id,
                'point_shift'=>$shiftDetails,
                'point_name' => $record->point->name,
                'date' => $record->date,
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
        $inspector = InspectorMission::where('inspector_id', $inspectorId)
                                     ->where('date', $today)
                                     ->where('day_off', 0)
                                     ->first();

        if ($request->id) {
            $record = paperTransaction::where('id', $request->id)->first();
            $isParent = $record->parent;

            // Initialize the images array
            $images = [];

            // Handle old images
            if (!empty($request->old_images)) {
                if (is_string($request->old_images)) {
                    $oldImages = explode(',', $request->old_images);
                } else {
                    $oldImages = $request->old_images; // In case it's already an array
                }

                if (is_array($oldImages)) {
                    $images = $oldImages;
                }
            }

            if ($isParent == 0) {
                // Mark the old record as inactive
                $record->status = 0;
                $record->save();

                // Create new paperTransaction record
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

                // Initialize $newImages as an empty array
                $newImages = [];

                // Handle new image upload
                if ($request->hasFile('images')) {
                    $files = $request->file('images');
                    $path = 'Api/images/paperTransactions';
                    $model = paperTransaction::find($new->id);
                    $newImages = $this->UploadFilesIM($path, 'images', $model, $files);

                    // Merge old and new images
                    $images = array_merge($images, $newImages);
                }

                // Save images as a comma-separated string
                $new->images = implode(',', $images);
                $new->save();

                $recor = paperTransaction::find($new->id);
                $success['report'] = $recor->only('id', 'point_id', 'mission_id', 'inspector_id', 'civil_number', 'registration_number', 'images', 'created_at');

                return $this->respondSuccess($success, 'Data saved successfully.');
            } else {
                // Deactivate child records of the same parent
                $records = paperTransaction::where('parent', $isParent)->pluck('id')->toArray();
                foreach ($records as $recordId) {
                    $recs = paperTransaction::find($recordId);
                    $recs->status = 0;
                    $recs->save();
                }

                // Create new paperTransaction record
                $new = new paperTransaction();
                $new->point_id = $request->point_id;
                $new->mission_id = $request->mission_id;
                $new->inspector_id = $inspectorId;
                $new->civil_number = $request->civil_number;
                $new->date = $request->date;
                $new->registration_number = $request->registration_number;
                $new->status = 1;
                $new->parent = $isParent;
                $new->created_by = auth()->user()->id;

                // Initialize $newImages as an empty array
                $newImages = [];

                // Handle new image upload
                if ($request->hasFile('images')) {
                    $files = $request->file('images');
                    $path = 'Api/images/paperTransactions';
                    $model = paperTransaction::find($new->id);
                    $newImages = $this->UploadFilesIM($path, 'images', $model, $files);

                    // Merge old and new images
                    $images = array_merge($images, $newImages);
                }

                // Save images as a comma-separated string
                $new->images = implode(',', $images);
                $new->save();

                $recor = paperTransaction::find($new->id);
                $success['report'] = $recor->only('id', 'point_id', 'mission_id', 'inspector_id', 'civil_number', 'registration_number', 'images', 'created_at');

                return $this->respondSuccess($success, 'Data saved successfully.');
            }
        } else {
            // Create new paperTransaction record
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

            // Initialize $newImages as an empty array
            $newImages = [];

            // Handle new image upload
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $path = 'Api/images/paperTransactions';
                $model = paperTransaction::find($new->id);
                $newImages = $this->UploadFilesIM($path, 'images', $model, $files);
            }

            // Save images as a comma-separated string
            $new->images = implode(',', $newImages);
            $new->save();

            $record = paperTransaction::find($new->id);
            $success['report'] = $record->only('id', 'point_id', 'mission_id', 'inspector_id', 'civil_number', 'registration_number', 'images', 'created_at');

            return $this->respondSuccess($success, 'Data saved successfully.');
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


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
