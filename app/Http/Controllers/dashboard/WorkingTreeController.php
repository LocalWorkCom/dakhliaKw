<?php

namespace App\Http\Controllers\dashboard;

use App\DataTables\IoTelegramDataTable;
use App\Http\Controllers\Controller;
use App\Models\WorkingTime;
use App\Models\WorkingTree;
use App\Models\WorkingTreeTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WorkingTreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('workingTree.index');
    }

    public function getWorkingTrees()
    {
        $WorkingTrees = WorkingTree::orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($WorkingTrees)
            ->rawColumns(['action'])
            ->make(true);
    }
    public function create()
    {
        $WorkingTimes = WorkingTime::all();
        return view('workingTree.add', compact('WorkingTimes'));
    }
    public function store(Request $request)
    {
        // Define the base validation rules
        $rules = [
            'name' => 'required|string',
            'working_days_num' => 'required|integer|min:1',
        ];

        $messages = [
            'name.required' => 'يجب ادخال اسم الادارة',
            'name.string' => 'يجب ادخال اسم الادارة',
            'working_days_num.required' => 'يجب ادخال عدد ايام العمل',
            'working_days_num.integer' => 'يجب ادخال رقم في عدد ايام العمل',
        ];

        // Dynamically add rules for holiday checkboxes and working times
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;

            if ($request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on') {
                // If holiday checkbox is checked, working time ID should be null
                $rules[$holidayPeriod] = 'nullable|exists:working_times,id';  // Allow null or existing working time
            } else {
                // If holiday checkbox is not checked, working time ID is required
                $rules[$holidayPeriod] = 'required|exists:working_times,id';
            }

            // Add custom messages for the working time ID validation
            $messages[$holidayPeriod . '.required'] = 'يجب اختيار وقت العمل للمدة ' . $i;
            $messages[$holidayPeriod . '.exists'] = 'وقت العمل المحدد غير موجود.';
        }
        // Check dynamic rules
        // $working_days_num = $request->input('working_days_num');
        // for ($i = 1; $i <= $working_days_num; $i++) {
        //     $rules["period{$i}"] = 'nullable|exists:working_times,id'; // Only required if not checked
        //     if ($request->input("holiday_checkbox{$i}") !== 'on') {
        //         $rules["period{$i}"] = 'required|exists:working_times,id'; // Required if not holiday
        //     }
        // }

        // Validate the request data
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withInput();
        }

        $holiday_days_num = 0;

        $WorkingTree = new WorkingTree;
        $WorkingTree->name = $request->name;
        $WorkingTree->working_days_num = $request->working_days_num;
        $WorkingTree->holiday_days_num = $holiday_days_num;
        $WorkingTree->created_by = auth()->id();
        $WorkingTree->created_departement = auth()->user()->department_id;
        $WorkingTree->save();

        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;

            // Create WorkingTreeTime entry
            $WorkingTreeTime = new WorkingTreeTime;
            $WorkingTreeTime->working_tree_id = $WorkingTree->id;
            $WorkingTreeTime->day_num = $i;
            $WorkingTreeTime->created_by = auth()->id();
            $WorkingTreeTime->created_departement = auth()->user()->department_id;

            // Check if holiday checkbox is checked
            if ($request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on') {
                $WorkingTreeTime->is_holiday = 1;
                $holiday_days_num++;
                $WorkingTreeTime->working_time_id = null; // Set working time ID to null for holidays
            } else {
                $WorkingTreeTime->is_holiday = 0;
                $WorkingTreeTime->working_time_id = $request->input($holidayPeriod); // Set the period if available
            }

            $WorkingTreeTime->save();
        }

        $WorkingTree->holiday_days_num = $holiday_days_num;
        
        $WorkingTree->working_days_num = $request->working_days_num - $holiday_days_num;
        $WorkingTree->save();

        session()->flash('success', 'تم الحفظ بنجاح.');

        return redirect()->route('working_trees.list');
    }

    public function edit($id)
    {
        $workingTimes = WorkingTime::all();
        $workingTree = WorkingTree::with('workingTreeTimes')->where('id', $id)->first();
        return view('workingTree.edit', compact('workingTimes', 'workingTree'));
    }
    public function update(Request $request, $id)
    {
        $rules = [

            'name' => 'required|string',
            'working_days_num' => 'required|integer|min:1',
            // 'holiday_days_num' => 'required|integer|min:0',
        ];

        $messages = [

            'name.required' => 'يجب ادخال اسم الادارة',
            'name.string' => 'يجب ادخال اسم الادارة',
            'working_days_num.required' => 'يجب ادخال عدد ايام العمل',
            'working_days_num.integer' => 'يجب ادخال رقم في عدد ايام العمل',
            // 'holiday_days_num.required' => 'يجب ادخال عدد ايام الاجازات',
            // 'holiday_days_num.integer' => 'يجب ادخال رقم في عدد ايام الاجازات',

        ];
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;

            if ($request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on') {
                // If holiday checkbox is checked, working time ID should be null
                $rules[$holidayPeriod] = 'nullable|exists:working_times,id';  // Allow null or existing working time
            } else {
                // If holiday checkbox is not checked, working time ID is required
                $rules[$holidayPeriod] = 'required|exists:working_times,id';
            }

            // Add custom messages for the working time ID validation
            $messages[$holidayPeriod . '.required'] = 'يجب اختيار وقت العمل للمدة ' . $i;
            $messages[$holidayPeriod . '.exists'] = 'وقت العمل المحدد غير موجود.';
        }
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());
            return redirect()->back()->withInput();
        }
        $holiday_days_num = 0;
        $WorkingTree =  WorkingTree::find($id);
        $WorkingTree->name = $request->name;
        $WorkingTree->working_days_num = $request->working_days_num;
        $WorkingTree->holiday_days_num = $holiday_days_num;
        // $WorkingTree->created_by = auth()->id();
        // $WorkingTree->created_departement = auth()->user()->department_id;
        $WorkingTree->save();
        if ($WorkingTree->working_days_num > $request->working_days_num) {
            for ($j = 0; $j < $WorkingTree->working_days_num; $j++) {
                if ($j != 1) {
                    $checkTimeExistDel = WorkingTreeTime::where('day_num', $j)->where('working_tree_id', $id)->first();
                    $checkTimeExistDel->delete();
                }
            }
        }
        for ($i = 1; $i <= $request->working_days_num; $i++) {
            $holidayCheckbox = "holiday_checkbox" . $i;
            $holidayPeriod = "period" . $i;

            // Create WorkingTreeTime entry
            $checkTimeExist = WorkingTreeTime::where('day_num', $i)->where('working_tree_id', $id)->first();
            // dd($checkTimeExist);
            if (!$checkTimeExist) {
                $WorkingTreeTime = new WorkingTreeTime;
                $WorkingTreeTime->working_time_id = $request->input("holiday" . $i); // Assuming you want to set working_time_id here
                $WorkingTreeTime->working_tree_id = $WorkingTree->id;
                $WorkingTreeTime->day_num = $i;
                $WorkingTreeTime->created_by = auth()->id();
                $WorkingTreeTime->created_departement = auth()->user()->department_id;

                if ($request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on') {
                    $WorkingTreeTime->working_time_id = null;
                    $WorkingTreeTime->is_holiday = 1; // Or any other logic for holidays
                    $holiday_days_num++;
                } else {
                    $WorkingTreeTime->is_holiday = 0;
                }

                // Set the period if available
                if ($request->has($holidayPeriod)) {
                    $WorkingTreeTime->working_time_id = $request->input($holidayPeriod);
                }

                $WorkingTreeTime->save();
            } else {
                $checkTimeExist->working_time_id = $request->input("holiday" . $i); // Assuming you want to set working_time_id here
                $checkTimeExist->working_tree_id = $WorkingTree->id;
                $checkTimeExist->day_num = $i;
                $checkTimeExist->created_by = auth()->id();
                $checkTimeExist->created_departement = auth()->user()->department_id;

                if ($request->has($holidayCheckbox) && $request->input($holidayCheckbox) === 'on') {
                    $checkTimeExist->working_time_id = null;
                    $checkTimeExist->is_holiday = 1; // Or any other logic for holidays
                    $holiday_days_num++;
                } else {
                    $checkTimeExist->is_holiday = 0;
                }

                // Set the period if available
                if ($request->has($holidayPeriod)) {
                    $checkTimeExist->working_time_id = $request->input($holidayPeriod);
                }

                $checkTimeExist->save();
            }
            // Check if holiday checkbox is checked
        }
        $WorkingTree->holiday_days_num = $holiday_days_num;
        $WorkingTree->working_days_num = $request->working_days_num - $holiday_days_num;
        $WorkingTree->save();
        session()->flash('success', 'تم التعديل بنجاح.');

        return redirect()->route('working_trees.list');
    }
    public function show($id)
    {
        $WorkingTree = WorkingTree::find($id);


        return view('workingTree.show', compact('WorkingTree'));
    }
}
