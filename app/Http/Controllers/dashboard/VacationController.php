<?php

namespace App\Http\Controllers\dashboard;

use Log;
use App\Models\Country;
use App\Models\Inspector;
use Illuminate\Http\Request;
use App\Models\EmployeeVacation;

use App\Models\InspectorMission;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id = 0)
    {
        // $filter = $request->query('filter');

        // Initialize query
        $vacations = EmployeeVacation::query();

        // Apply filter if specified
        // if ($filter) {
        //     switch ($filter) {
        //         case 'exceeded':
        //             $vacations->where('is_exceeded', '=', 1);
        //             break;
        //         case 'finished':
        //             $vacations->where('end_date', '<', now()->toDateString());
        //             break;
        //         case 'current':
        //             $vacations->where('start_date', '=', now()->toDateString())
        //                 ->where('status', '=', 'Approved');
        //             break;
        //         case 'not_begin':
        //             $vacations->where('start_date', '>', now()->toDateString());
        //             break;
        //     }
        // }

        // Use for DataTable response
        // if ($request->ajax()) {
        //     return datatables()->of($vacations)
        //         ->addColumn('action', function ($vacation) {
        //             return view('vacation.partials.action-buttons', compact('vacation'));
        //         })
        //         ->make(true);
        // }
        $vacationCount = $vacations->count();
        $vacations = $vacations->with('employee', 'vacation_type')->orderby('created_at', 'desc')->get();
        $EmployeeVacations = EmployeeVacation::all();
        $data_filter = [];
        $exceeded = 0;
        $finished = 0;
        $current = 0;
        $not_begin = 0;
        $pending = 0;
        $rejected = 0;
        foreach ($EmployeeVacations as  $EmployeeVacation) {
            if (GetEmployeeVacationType($EmployeeVacation) == 'متجاوزة') {
                $exceeded++;
            } else if (GetEmployeeVacationType($EmployeeVacation) == 'لم تبدأ بعد') {
                $not_begin++;
            } else if (GetEmployeeVacationType($EmployeeVacation) == 'حالية') {
                $current++;
            } else if (GetEmployeeVacationType($EmployeeVacation) == 'منتهية') {
                $finished++;
            } else if (GetEmployeeVacationType($EmployeeVacation) == 'مرفوضة') {
                $rejected++;
            } else if (GetEmployeeVacationType($EmployeeVacation) == 'مقدمة') {
                $pending++;
            }
        }
        $data_filter['current'] = $current;
        $data_filter['finished'] = $finished;
        $data_filter['exceeded'] = $exceeded;
        $data_filter['not_begin'] = $not_begin;
        $data_filter['rejected'] = $rejected;
        $data_filter['pending'] = $pending;
        // Count based on filters
        // $exceeded = EmployeeVacation::where('is_exceeded', '=', 1)->count();
        // $finished = EmployeeVacation::where('end_date', '<', now()->toDateString())->count();
        // $current = EmployeeVacation::where('start_date', '=', now()->toDateString())
        //     ->where('status', '=', 'Approved')
        //     ->where('is_exceeded', '!=', '1') // Exclude records where `is_exceeded` is 1
        //     ->count();

        // $not_begin = EmployeeVacation::where('start_date', '>', now()->toDateString())
        //     ->where('status', '!=', 'Pending')
        //     ->count();
        // dd($current);
        // Pass results and counts to the view
        return view('vacation.index', compact('id', 'vacations', 'data_filter', 'vacationCount'));
    }

    public function getVacations($id, Request $request)
    {
        if ($id) {

            $EmployeeVacations = EmployeeVacation::where('employee_id', $id)
                ->with('employee', 'vacation_type')
                ->orderby('created_at', 'desc')
                ->get();
            foreach ($EmployeeVacations->clone()->get() as  $EmployeeVacation) {
                # code...
                $EmployeeVacation['VacationStatus'] = GetEmployeeVacationType($EmployeeVacation);

                if ($EmployeeVacation->status == 'Rejected') {
                    // If status is 'Rejected' and end_date is not set, use a placeholder
                    $EmployeeVacation['StartVacation'] = '______________';
                } else {
                    // If neither condition is met, use the expected end date from another function
                    $EmployeeVacation['StartVacation'] = CheckStartVacationDate($EmployeeVacation->id);
                }

                if ($EmployeeVacation->end_date) {
                    // If end_date is set, add 1 day to it
                    $EmployeeVacation['EndDate'] = $EmployeeVacation->end_date;
                } elseif ($EmployeeVacation->status == 'Rejected') {
                    // If status is 'Rejected' and end_date is not set, use a placeholder
                    $EmployeeVacation['EndDate'] = '______________';
                } else {
                    // If neither condition is met, use the expected end date from another function
                    $EmployeeVacation['EndDate'] = ExpectedEndDate($EmployeeVacation)[0];
                }
                if ($EmployeeVacation->end_date) {
                    // If end_date is set, add 1 day to it
                    $EmployeeVacation['StartWorkDate'] = AddDays($EmployeeVacation->end_date, 1);
                } elseif ($EmployeeVacation->status == 'Rejected') {
                    // If status is 'Rejected' and end_date is not set, use a placeholder
                    $EmployeeVacation['StartWorkDate'] = '______________';
                } else {
                    // If neither condition is met, use the expected end date from another function
                    $EmployeeVacation['StartWorkDate'] = ExpectedEndDate($EmployeeVacation)[1];
                }
                $daysLeft = VacationDaysLeft($EmployeeVacation);
                $currentDate = date('Y-m-d');

                if ($EmployeeVacation->start_date > $currentDate) {
                    // Vacation has not started yet
                    $EmployeeVacation['DaysLeft'] = 'لم تبدا بعد';
                } else {
                    // Vacation has started, check days left
                    if ($EmployeeVacation->is_cut) {
                        $EmployeeVacation['DaysLeft'] = 0;
                    } else {
                        if ($daysLeft >= 0) {
                            $EmployeeVacation['DaysLeft'] = $daysLeft;
                        } else {
                            $EmployeeVacation['DaysLeft'] = 'متجاوز';
                        }
                    }
                }
            }


            // if ($request->has('vacation') && $request->vacation) {
            //     $EmployeeVacations->where('VacationStatus', $request->vacation);
            // }

            // $EmployeeVacations = $EmployeeVacations->get();
            return DataTables::of($EmployeeVacations)

                ->rawColumns(['action'])
                ->make(true);
        } else {
            $EmployeeVacations = EmployeeVacation::with('employee', 'vacation_type')
                ->orderby('created_at', 'desc')->get();

            foreach ($EmployeeVacations as  $EmployeeVacation) {

                $EmployeeVacation['VacationStatus'] = GetEmployeeVacationType($EmployeeVacation);

                if ($EmployeeVacation->status == 'Rejected' || $EmployeeVacation->status == 'Pending') {
                    // If status is 'Rejected' and end_date is not set, use a placeholder
                    $EmployeeVacation['StartVacation'] = '______________';
                } else {
                    // If neither condition is met, use the expected end date from another function
                    $EmployeeVacation['StartVacation'] = CheckStartVacationDate($EmployeeVacation->id);
                }

                if ($EmployeeVacation->end_date) {
                    // If end_date is set, add 1 day to it
                    $EmployeeVacation['EndDate'] = $EmployeeVacation->end_date;
                } elseif ($EmployeeVacation->status == 'Rejected' || $EmployeeVacation->status == 'Pending') {
                    // If status is 'Rejected' and end_date is not set, use a placeholder
                    $EmployeeVacation['EndDate'] = '______________';
                } else {
                    // If neither condition is met, use the expected end date from another function
                    $EmployeeVacation['EndDate'] = ExpectedEndDate($EmployeeVacation)[0];
                }
                if ($EmployeeVacation->end_date) {
                    // If end_date is set, add 1 day to it
                    $EmployeeVacation['StartWorkDate'] = AddDays($EmployeeVacation->end_date, 1);
                } elseif ($EmployeeVacation->status == 'Rejected' || $EmployeeVacation->status == 'Pending') {
                    // If status is 'Rejected' and end_date is not set, use a placeholder
                    $EmployeeVacation['StartWorkDate'] = '______________';
                } else {
                    // If neither condition is met, use the expected end date from another function
                    $EmployeeVacation['StartWorkDate'] = ExpectedEndDate($EmployeeVacation)[1];
                }
                // if ($EmployeeVacation->status == 'Rejected') {

                //     $EmployeeVacation['startDate'] = '________';
                //     $EmployeeVacation['daysNumber'] = '________';
                // } else {
                $EmployeeVacation['startDate'] = $EmployeeVacation->start_date;
                $EmployeeVacation['daysNumber'] = $EmployeeVacation->days_number;
                // }





                $daysLeft = VacationDaysLeft($EmployeeVacation);
                $currentDate = date('Y-m-d');
                if ($EmployeeVacation->status == 'Rejected' || $EmployeeVacation->status == 'Pending') {
                    $EmployeeVacation['DaysLeft'] = '______________';
                } else {
                    if ($EmployeeVacation->start_date > $currentDate) {
                        // Vacation has not started yet
                        $EmployeeVacation['DaysLeft'] = 'لم تبدا بعد';
                    } else {
                        // Vacation has started, check days left
                        if ($EmployeeVacation->is_cut) {
                            $EmployeeVacation['DaysLeft'] = 'مقطوعة';
                        } else {
                            if ($daysLeft >= 0) {
                                $EmployeeVacation['DaysLeft'] = $daysLeft;
                            } else {
                                if ($EmployeeVacation->end_date) {
                                    $EmployeeVacation['DaysLeft'] = 'منتهية';
                                } else {
                                    $EmployeeVacation['DaysLeft'] = 'متجاوز';
                                }
                            }
                        }
                    }
                }
            }
            // if ($request->has('vacation') && $request->vacation) {
            //     $EmployeeVacations->where('VacationStatus', $request->vacation);
            // }

            // $EmployeeVacations = $EmployeeVacations->get();
            return DataTables::of($EmployeeVacations)


                ->rawColumns(['action'])
                ->make(true);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($id = 0)
    {

        $employees = getEmployees();
        $vacation_types = getVactionTypes();
        if ($id) {

            $vacation_types = getVactionTypes()->where('id', '<>', '3');
        } else {
            $vacation_types = getVactionTypes();
        }
        $countries = getCountries();
        return view('vacation.add', compact('employees', 'vacation_types', 'id', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $rules = [
            'vacation_type_id' => 'required',
            'start_date' => 'required|date',
            'days_num' => 'required|integer|min:1', // Added validation for days_num
            'employee_id' => 'required',
        ];

        if ($request->has('check_country')) {
            $rules['country_id'] = 'required';
        }


        $messages = [
            'vacation_type_id.required' => 'يجب ادخال نوع الاجازة',
            'start_date.required' => 'يجب ادخال تاريخ البداية',
            'days_num.required' => 'يجب ادخال عدد الأيام', // Added custom message for days_num
            'employee_id.required' => 'يجب عليك اختيار موظف',
            'country_id.required' => 'يجب اختيار دولة عند تحديد دولة خارجية',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());
            return redirect()->route('vacation.add', $id)
                ->withInput(); // Retain input values
        }

        if ($id == 0) {
            $employee_id = $request->employee_id;
        } else {
            $employee_id = $id;
        }

        $check_vacation = EmployeeVacation::where('employee_id', $employee_id)->get();
        // pending
        foreach ($check_vacation as $value) {
            if ($value->status == 'Pending') {
                $ExpectedEndDate = ExpectedEndDate($value)[0];

                if ($ExpectedEndDate >= $request->start_date && $value->start_date <= $request->start_date) {
                    return redirect()->route('vacation.add', $id)->withErrors(['يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف']);
                }
            } elseif ($value->status != 'Rejected' && $value->end_date) {
                if ($value->end_date >= $request->start_date && $value->start_date <= $request->start_date) {
                    return redirect()->route('vacation.add', $id)->withErrors(['يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف']);
                }
            }
            //not rejected
            elseif ($value->status != 'Rejected' && !$value->end_date) {
                $currentDate = date('Y-m-d');
                $ExpectedEndDate = ExpectedEndDate($value)[0];

                if ($currentDate <= $request->start_date && $value->start_date <= $request->start_date && $ExpectedEndDate >= $request->start_date) {
                    return redirect()->route('vacation.add', $id)->withErrors(['يوجد اجازة اخرى بنفس تاريخ البداية أو في نطاق التواريخ لنفس الموظف']);
                }
            }
        }



        $employee_vacation = new EmployeeVacation();
        $employee_vacation->vacation_type_id = $request->vacation_type_id;
        $employee_vacation->start_date = $request->start_date;
        $employee_vacation->days_number = $request->days_num;
        $employee_vacation->country_id = $request->country_id;
        $employee_vacation->employee_id = $employee_id;
        $employee_vacation->created_by = auth()->id();
        $employee_vacation->created_departement = auth()->user()->department_id;
        $employee_vacation->save();

        if ($request->hasFile('reportImage')) {
            $file = $request->reportImage;
            $path = 'vacations/employee';

            UploadFiles($path, 'report_image', 'report_image_real', $employee_vacation, $file);
        }

        session()->flash('success', 'تم الحفظ بنجاح.');
        return redirect()->route('vacations.list', $id);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $vacation = EmployeeVacation::with('employee', 'vacation_type', 'country')->where('id', $id)->first();
        $employees = getEmployees();
        $vacation_types = getVactionTypes();

        return view('vacation.show', compact('vacation', 'employees', 'vacation_types', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employees = getEmployees();
        $vacation = EmployeeVacation::find($id);
        if ($vacation->employee_id) {

            $vacation_types = getVactionTypes()->where('id', '<>', '3');
        } else {
            $vacation_types = getVactionTypes();
        }
        $countries = getCountries();

        return view('vacation.edit', compact('employees', 'vacation', 'vacation_types', 'id', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $rules = [
            'vacation_type_id' => 'required',
            'date_from' => 'required|date|before_or_equal:date_to',
            'date_to' => 'required|date|after_or_equal:date_from',

        ];

        $messages = [

            'vacation_type_id.required' => 'يجب ادخال نوع الاجازة',
            'date_from.required' => 'يجب ادخال تاريخ البداية',
            'date_to.required' => 'يجب ادخال تاريخ النهاية',
            'date_from.before_or_equal' => 'تاريخ البداية يجب ان يكون قبل او يساوي تاريخ النهاية',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب ان يكون بعد او يساوي تاريخ البداية',
        ];
        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            session()->flash('errors', $validatedData->errors());

            return redirect()->route('vacations.list', $id);
        }

        $employee_vacation =  EmployeeVacation::find($id);
        $employee_vacation->vacation_type_id = $request->vacation_type_id;
        $employee_vacation->date_from = $request->date_from;
        $employee_vacation->date_to = isset($request->date_to) ? $request->date_to : null;
        $employee_vacation->employee_id = ($request->employee_id && $request->vacation_type_id != 3) ? $request->employee_id : null;
        $employee_vacation->created_by = auth()->id();
        $employee_vacation->created_departement = auth()->user()->department_id;
        $employee_vacation->save();
        if ($request->hasFile('reportImage')) {
            $file = $request->reportImage;
            // You can modify the UploadFiles function call according to your needs
            $path = 'vacations/employee';

            UploadFiles($path, 'report_image', 'report_image_real', $employee_vacation, $file);
        }
        session()->flash('success', 'تم التعديل بنجاح.');
        if ($request->employee_id) {
            return redirect()->route('vacations.list', $request->employee_id);
        } else {

            return redirect()->route('vacations.list');
        }
    }
    public  function delete($id)
    {
        $EmployeeVacation = EmployeeVacation::find($id);
        $EmployeeVacation->delete();
        session()->flash('success', 'تم الحذف بنجاح.');

        return redirect()->route('vacations.list');
    }
    public function downlaodfile($id)
    {
        $file = EmployeeVacation::find($id);
        // $download=downloadFile($file->file_name,$file->real_name);
        $file_path = public_path($file->file_name);
        $file_name = basename($file->real_name);

        return response()->download($file_path, $file_name);
        //echo 'downloaded';
    }

    public function acceptVacation($id)
    {
        // Find the vacation record
        $vacation = EmployeeVacation::find($id);

        if ($vacation) {
            // Update the status to Approved
            $vacation->status = 'Approved';
            $vacation->save();

            // Find the inspector based on employee_id
            $inspector = Inspector::where('user_id', $vacation->employee_id)->first();

            if ($inspector) {
                // Fetch InspectorMission records for the found inspector ID
                $inspectorMissions = InspectorMission::where('inspector_id', $inspector->id)
                    ->whereDate('date', '>=', $vacation->start_date)
                    ->get();

                if ($inspectorMissions->isEmpty()) {
                    session()->flash('info', 'لا توجد مهام للمفتش لتحديثها.');
                } else {
                    $daysNumber = $vacation->days_number; // Ensure this field exists in your EmployeeVacation model

                    foreach ($inspectorMissions as $mission) {
                        $mission_date =  Carbon::parse($mission->date);
                        $team_id = $mission->group_team_id;
                        $group_id = $mission->group_id;
                        // Check if the mission date is within the vacation period
                        if ($mission_date->diffInDays($vacation->start_date) < $daysNumber) {
                            // Update the InspectorMission record with the vacation ID
                            $mission->vacation_id = $vacation->id;
                            $mission->ids_group_point = null;
                            // $mission->status = 'Canceled'; // Or another appropriate status
                            $mission->save();
                        }
                    }
                    //call notification

                    session()->flash('success', 'تمت الموافقة على الإجازة بنجاح وتم تحديث المهام الخاصة بالمفتش.');
                }
                $EndDate = ExpectedEndDate($vacation)[0];
                    $inspectors = InspectorMission::where('group_team_id', $mission->group_team_id)->where('vacation_id', null)->whereBetween('date', [$vacation->start_date, $EndDate])->count();
                    
                    if ($inspectors < 2) {
                        $title = 'تنبيه من دوريات';
                        $message = '   بعد اجازه مفتش هذه الدوريه أصبح بها مفتش واحد';

                        $users = User::where('rule_id', 2)->get();
                        foreach ($users as $user) {
                            send_push_notification(null, $user->fcm_token, $title, $message);
                            $notify = new Notification();
                            $notify->message = $message;
                            $notify->title = $title;
                            $notify->group_id = $group_id;
                            $notify->team_id = $team_id;
                            $notify->user_id =  $user->id;
                            $notify->status = 0;
                            $notify->save();
                        }
                    }
            } else {
                session()->flash('error', 'المفتش غير موجود.');
                return redirect()->back();
            }
        } else {
            session()->flash('error', 'الإجازة غير موجودة.');
        }

        return redirect()->back();
    }

    public function rejectVacation($id)
    {
        // Find the vacation record
        $vacation = EmployeeVacation::find($id);

        if ($vacation) {
            // Update the status to Rejected
            $vacation->status = 'Rejected';
            $vacation->save();

            session()->flash('success', 'تم رفض الإجازة بنجاح.');
        } else {
            session()->flash('error', 'الإجازة غير موجودة.');
        }
        return redirect()->back();

        // return redirect()->route('vacations.list', $vacation->employee_id ?? null);
    }
    public function updateVacation(Request $request, $id)
    {
        $vacation = EmployeeVacation::find($id);
        if ($vacation) {
            if ($request->type == 'cut') {
                $inspector = Inspector::where('user_id', $vacation->employee_id)->first();

                if ($inspector) {
                    // Fetch InspectorMission records for the found inspector ID
                    $inspectorMissions = InspectorMission::where('inspector_id', $inspector->id)
                        ->whereDate('date', '>=', $vacation->start_date)
                        ->get();

                    if ($inspectorMissions->isEmpty()) {
                        session()->flash('info', 'لا توجد مهام للمفتش لتحديثها.');
                    } else {
                        $end_date = $request->end_date;
                        $end_date = Carbon::parse($end_date);
                        $start_date =  Carbon::parse($vacation->start_date);
                        $daysNumber = $start_date->diffInDays($end_date, false) + 1;
                        $vacation->days_number = $daysNumber;
                        $vacation->save(); // Ensure this field exists in your EmployeeVacation model
                        foreach ($inspectorMissions as $index => $mission) {
                            // Check if the mission date is within the vacation period

                            $mission_date =  Carbon::parse($mission->date);

                            if ($mission_date->diffInDays($vacation->start_date) < $daysNumber) {

                                // Update the InspectorMission record with the vacation ID
                                $mission->vacation_id = $vacation->id;
                                $mission->ids_group_point = null;

                                // $mission->status = 'Canceled'; // Or another appropriate status
                                $mission->save();
                            } else {
                                // dd($mission);
                                $mission->vacation_id  = null;
                                $mission->save();
                            }
                        }
                        session()->flash('success', 'تمت الموافقة على الإجازة بنجاح وتم تحديث المهام الخاصة بالمفتش.');
                    }
                }
                $vacation->is_cut = 1;

                $vacation->end_date = $request->end_date;
            } else if ($request->type == 'exceed') {
                $vacation->is_exceed = 1;
                $vacation->end_date = Carbon::parse($request->end_date)->subDay();
            } elseif ($request->type == 'direct_exceed') {
                // dd(0);
                $inspector = Inspector::where('user_id', $vacation->employee_id)->first();

                if ($inspector) {
                    $end_date = $request->end_date;
                    $end_date = Carbon::parse($end_date);
                    $start_date =  Carbon::parse($vacation->start_date);
                    $daysNumber = $start_date->diffInDays($end_date, false) + 1;
                    if (!$vacation->is_exceeded) {
                        $vacation->days_number = $daysNumber;
                        $vacation->save();
                    }
                    // Fetch InspectorMission records for the found inspector ID
                    $inspectorMissions = InspectorMission::where('inspector_id', $inspector->id)

                        ->whereDate('date', '>=', $vacation->start_date)
                        ->get();

                    if ($inspectorMissions->isEmpty()) {
                        session()->flash('info', 'لا توجد مهام للمفتش لتحديثها.');
                    } else {
                     
                        // Ensure this field exists in your EmployeeVacation model
                        foreach ($inspectorMissions as $index => $mission) {
                            // Check if the mission date is within the vacation period

                            $mission_date =  Carbon::parse($mission->date);
                            // if($index == 2){

                            //     dd($daysNumber,$mission_date->diffInDays($vacation->start_date),$start_date,$end_date);
                            // }
                            if ($mission_date->diffInDays($vacation->start_date) < $daysNumber) {

                                // Update the InspectorMission record with the vacation ID
                                $mission->vacation_id = $vacation->id;
                                // $mission->status = 'Canceled'; // Or another appropriate status
                                $mission->save();
                            } else {
                                $mission->vacation_id  = null;
                                $mission->save();
                            }
                        }
                    }
                }
                if (!$vacation->is_exceeded) {

                    $vacation->end_date = $request->end_date;
                } else {
                    $vacation->end_date = Carbon::parse($request->end_date)->subDay();
                }
            } elseif ($request->type == 'direct_work') {
                $vacation->end_date = Carbon::parse($request->end_date)->subDay();
            }


            $vacation->save();
        }
        return true;
    }

    public function print_returnVacation($id)
    {
        // Fetch the vacation record based on the provided ID
        $vacation = EmployeeVacation::find($id);
        // Return the view with the data to be printed
        return view('vacation.returnback', compact('vacation'));
    }
    public function permitVacation($id)
    {
        // Fetch the vacation record based on the provided ID
        $vacation = EmployeeVacation::find($id);
        // Return the view with the data to be printed
        return view('vacation.requestVacation', compact('vacation'));
    }


    // public function printVacation($id)
    // {
    //     // Fetch the vacation record based on the provided ID
    //     $vacation = EmployeeVacation::find($id);

    //     // Prepare any data needed for the view, such as related employee, vacation type, etc.
    //     // $relatedData = ...

    //     // Return the view with the data to be printed
    //     return view('vacation.returnback', compact('vacation'));
    // }

}
