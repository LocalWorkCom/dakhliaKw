<?php

use App\Models\Country;
use App\Models\departements;
use App\Models\EmployeeVacation;
use App\Models\Government;
use App\Models\Groups;
use App\Models\Io_file;
use App\Models\Sector;
use App\Models\User;
use App\Models\VacationType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


if (!function_exists('whats_send')) {
    function whats_send($mobile, $message, $country_code)
    {

        // dd("ss");
        $mobile = $country_code . $mobile;
        // dd($mobile);
        $params = array(
            'token' => 'rouxlvet3m3jl0a3',
            'to' => $mobile,
            'body' => $message
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ultramsg.com/instance31865/messages/chat",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        // dd($err);
        curl_close($curl);
        return $response;
    }
}

if (!function_exists('send_sms_code_msg')) {
    function send_sms_code_msg($msg, $phone, $country_code)
    {
        $phone = $country_code . $phone;
        $url = "http://62.150.26.41/SmsWebService.asmx/send";
        $params = array(
            'username' => 'Electron',
            'password' => 'LZFDD1vS',
            'token' => 'hjazfzzKhahF3MHj5fznngsb',
            'sender' => '7agz',
            'message' => $msg,
            'dst' => $phone,
            'type' => 'text',
            'coding' => 'unicode',
            'datetime' => 'now'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);

        if (curl_errno($ch) !== 0) {
            error_log('cURL error when connecting to ' . $url . ': ' . curl_error($ch));
        }

        // dd($result);
        curl_close($ch);

        // if ($result) {

        //   $status = "success";


        // } else {

        //  // echo $response;
        // }
        // return $status;

    }
}

if (!function_exists('send_sms_code')) {
    function send_sms_code($msg, $phone, $country_code)
    {

        // dd("Ff");
        $response = whats_send($phone, $msg, $country_code);
        //  dd($ff);
        return $response;

        //  send_sms_code_msg($msg, $phone, $country_code);
    }
}
/**
 * Upload Files
 * @path =>physical path to save files in
 * @image => name of file image in database
 * @realname =>real name file in db
 * @model => $model where to save files in
 * @request => the file input request which holds the file uploading 
 */

if (!function_exists('UploadFiles')) {

    function UploadFiles($path, $image, $realname, $model, $request)
    {

        $thumbnail = $request;
        $destinationPath = $path;
        $filerealname = $thumbnail->getClientOriginalName();
        $filename = $model->id . time() . '.' . $thumbnail->getClientOriginalExtension();
        // $destinationPath = asset($path) . '/' . $filename;
        $thumbnail->move($destinationPath, $filename);
        // $thumbnail->resize(1080, 1080);
        //  $thumbnail = Image::make(public_path() . '/'.$path.'/' . $filename);
        //Storage::move('public')->put($destinationPath, file_get_contents($thumbnail));

        $model->$image = asset($path) . '/' . $filename;
        $model->$realname = asset($path) . '/' . $filerealname;

        $model->save();
    }
}
function generateUniqueNumber($counter)
{

    //static $counter = 0 ; // Static variable to keep track of the counter
    $today = Carbon::today();
    $year = $today->year;
    $month = sprintf("%02d", $today->month); // Add leading zero if month is less than 10
    $day = sprintf("%02d", $today->day); // Add leading zero if day is less than 10

    $formattedDate = $year . '-' . $month . $day;
    // Increment the counter

    $incrementedCounter = str_pad($counter + 1, 4, '0', STR_PAD_LEFT);
    //dd($incrementedCounter);
    //$incrementedCounter++;
    $formattedNumber = $formattedDate . '-' . $incrementedCounter;

    return ['formattedNumber' => $formattedNumber, 'counter' => $counter + 1];
}


function UploadFilesWithoutReal($path, $image, $model, $request)
{

    $thumbnail = $request;
    $destinationPath = $path;
    $filename = time() . '.' . $thumbnail->getClientOriginalExtension();
    $thumbnail->move($destinationPath, $filename);

    $model->$image = asset($path) . '/' . $filename;

    $model->save();
}
function UploadFilesIM($path, $image, $model, $request)
{

    // dd($request);

    $imagePaths = [];
    $thumbnail = $request;
    $destinationPath = $path;
    foreach ($thumbnail as $key => $item) {
        if (is_object($item)) {
            $filename = $key . time() . '.' . $item->getClientOriginalExtension();
            $item->move($destinationPath, $filename);
            $imagePaths[] = asset($path) . '/' . $filename;
        } else {
            $imagePaths[] = $item;
        }
    }
    // dd($model->image);
    $model->$image = implode(',', $imagePaths);

    $model->save();
}


function showUserDepartment()
{
    // Retrieve the authenticated user
    $user = Auth::user();

    // Access the department name
    // dd($user->department);
    $departmentName = $user->department != null ? $user->department->name : '';

    return $departmentName;
}
function CheckUploadIoFiles($id)
{
    $count = Io_file::where('iotelegram_id', $id)->count();
    if ($count > 0) {
        return true;
    }
    return false;
}
function getEmployees()
{
    return User::all();
}
function getDepartments()
{
    return departements::all();
}
function getVactionTypes()
{
    return VacationType::all();
}
function getCountries()
{
    return  Country::all();
}
function getgovernments()
{

    return  Government::all();
}
function getsectores()
{

    return  Sector::all();
}
function getgroups()
{

    return  Groups::all();
}
function CheckStartVacationDate($id)
{
    $EmployeeVaction =  EmployeeVacation::find($id);
    if ($EmployeeVaction->date_from > date('Y-m-d')) {
        return true;
    }
    return false;
}
############################################## Vacation #######################################################################
function GetEmployeeVacationType($employeeVacation)
{
    $introduce = 'مقدمة';
    $rejected = 'مرفوضة';
    $exceeded = 'متجاوزة';
    $current = 'حالية';
    $notBegin = 'لم تبدأ بعد';
    $finished = 'منتهية';
    $today = date('Y-m-d');
    $expectedEndDate = ExpectedEndDate($employeeVacation)[0];

    // Save the calculated end date to  model
    // $expected_date->toDateString();

    if ($employeeVacation->status == 'Pending') {
        return $introduce;
    } else if ($employeeVacation->status == 'Rejected') {
        return $rejected;
    } else {
        if ($employeeVacation->start_date > $today) {
            return $notBegin;
        } else if ($employeeVacation->start_date < $today && $expectedEndDate < $today) {
            if ($employeeVacation->is_exceeded) {
                return $exceeded;
            } else {
                return $finished;
            }
        } else {
            return $current;
        }
    }
}
function VacationDaysLeft($employeeVacation)
{
    $startDate = $employeeVacation->start_date;
    $daysNumber = $employeeVacation->days_number;
    $today = date('Y-m-d');


    $startDate = Carbon::parse($employeeVacation->start_date);
    $daysNumber = $employeeVacation->days_number;

    // Calculate the end date
    $endDate = $startDate->copy()->addDays($daysNumber);

    // Get today's date
    $today = Carbon::today();

    // Calculate the number of days left
    $daysLeft = $today->diffInDays($endDate, false);

    if ($daysLeft < 0) {
        return -1;
    }
    return $daysLeft;
}
function ExpectedEndDate($employeeVacation)
{
    $startDate = Carbon::parse($employeeVacation->start_date);
    $daysNumber = $employeeVacation->days_number - 1;

    $expectedEndDate = $startDate->copy()->addDays($daysNumber);
    $workStartdDate = $expectedEndDate->copy()->addDays(1);
    return [$expectedEndDate->toDateString(), $workStartdDate->toDateString()];
}
