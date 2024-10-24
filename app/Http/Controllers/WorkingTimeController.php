<?php

namespace App\Http\Controllers;

use App\Models\WorkingTime;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreWorkingTimeRequest;
use App\Http\Requests\UpdateWorkingTimeRequest;
use Illuminate\Validation\Rule;

class WorkingTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        return view('working_time.index');
    }

    public function getWorkingTime()
    {
        $data = WorkingTime::all();
        // dd($data);

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
        //
        // dd($request);

        $messages = [
            'name.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
            'start_time.required' => 'بداية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
            'end_time.required' => 'نهاية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
            'color.unique' => 'اللون المحدد موجود بالفعل، يرجى اختيار لون آخر.',

        ];
        $validatedData = Validator::make($request->all(), [
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'color' => 'required|unique:working_times,color',
        ], $messages);

        // Handle validation failure
        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        try {

            $WorkingTime = new WorkingTime();
            $WorkingTime->name = $request->name;
            $WorkingTime->start_time = $request->start_time;
            $WorkingTime->end_time = $request->end_time;
            // dd($WorkingTime);
            // Generate a random color that is not in the database
            /*do {
                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                $existColors = ['#000000ab', '#ffffff', '#d6d6d6', '#fdfdfdc2', '#c9f5f9', '#4edfd0ba'];
            } while (WorkingTime::where('color', $color)->whereNotIn('color', $existColors)->exists());*/

            $WorkingTime->color = $request->color;
            $WorkingTime->save();
            // Dynamically create model instance based on the model class string
            return view('working_time.index')->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $workingTime = WorkingTime::find($id);
        // dd($workingTime);
        if ($workingTime) {
            return response()->json(['success' => true, 'data' => $workingTime]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $workingTime = WorkingTime::find($id);
        if ($workingTime) {
            return response()->json(['success' => true, 'data' => $workingTime]);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $workingTime = WorkingTime::find($request->id_edit);

        // Check if the working time record exists
        if (!$workingTime) {
            return redirect()->back()->withErrors('No working time found with the provided ID.');
        }
        $messages = [
            'name_edit.required' => 'الاسم مطلوب ولا يمكن تركه فارغاً.',
            'start_time_edit.required' => 'بداية فترة العمل مطلوبة ولا يمكن تركها فارغة.',
            'end_time_edit.required' => 'نهاية فترة العمل مطلوبة ولا يمكن تركها فارغة.',
            'color_edit.unique' => 'اللون المحدد موجود بالفعل، يرجى اختيار لون آخر.',
        ];

        $validatedData = Validator::make($request->all(), [
            'name_edit' => 'required',
            'start_time_edit' => 'required',
            'end_time_edit' => 'required',
            'color_edit' => [
                'required',
                Rule::unique('working_times', 'color')->ignore($workingTime->id), // Ignore current record's color
            ],        ], $messages);

        if ($validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try {
            $WorkingTimeitem = WorkingTime::findOrFail($request->id_edit);
            $WorkingTimeitem->name = $request->name_edit;
            $WorkingTimeitem->start_time = $request->start_time_edit;
            $WorkingTimeitem->end_time = $request->end_time_edit;
            $WorkingTimeitem->color = $request->color_edit;
            $WorkingTimeitem->save();

            return redirect()->route('working_time.index')->with('success', 'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }



    // public function update(Request $request)
    // {
    //     // dd($request);
    //     $messages = [
    //         'name_edit.required' => 'الاسم  مطلوب ولا يمكن تركه فارغاً.',
    //         'start_time_edit.required' => 'بداية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
    //         'end_time_edit.required' => 'نهاية فترة العمل   مطلوب ولا يمكن تركه فارغاً.',
    //     ];
    //     $validatedData = Validator::make($request->all(), [
    //         'name_edit' => 'required',
    //         'start_time_edit' => 'required',
    //         'end_time_edit' => 'required',
    //     ], $messages);
    //     // Handle validation failure
    //     if ($validatedData->fails()) {
    //         return redirect()->back()->withErrors($validatedData)->withInput();
    //     }
    //     try {
    //         $WorkingTimeitem = WorkingTime::findOrFail($request->id_edit);
    //         $WorkingTimeitem->name = $request->name_edit;
    //         $WorkingTimeitem->start_time = $request->start_time_edit;
    //         $WorkingTimeitem->end_time = $request->end_time_edit;
    //         $WorkingTimeitem->save();
    //         // Dynamically create model instance based on the model class string
    //         return view('working_time.index')->with('success', 'Permission created successfully.');
    //     } catch (\Exception $e) {
    //         return response()->json($e->getMessage());
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkingTime $workingTime)
    {
        //
    }
}
