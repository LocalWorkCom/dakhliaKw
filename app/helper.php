<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Groups;
use App\Models\Sector;
use App\Models\Country;
use App\Models\Io_file;
use App\Models\Inspector;
use App\Models\Government;
use App\Models\departements;
use App\Models\VacationType;
use App\Models\EmployeeVacation;
use App\Models\GroupSectorHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\InspectorGroupHistory;
use App\Models\Notification;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\App;

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


function getLatLongFromUrl($url)
{
    $shortenerDomains = [
        'bit.ly',
        'goo.gl',
        't.co',
        'tinyurl.com',
        'ow.ly',
        'buff.ly',
        'is.gd',
        'tiny.cc',
        'maps.app.goo.gl',
    ];

    // Parse the domain from the URL
    $host = parse_url($url, PHP_URL_HOST);
    if (in_array($host, $shortenerDomains)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_exec($ch);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
    }

    // Check for @lat,long format or other formats
    $pattern = '/@([-+]?[0-9]*\.?[0-9]+),([-+]?[0-9]*\.?[0-9]+)/';
    preg_match($pattern, $url, $matches);

    // Additional regex for Google Maps URL with query parameters
    if (!$matches) {
        $pattern = '/([-+]?[0-9]*\.?[0-9]+)[, ]+([-+]?[0-9]*\.?[0-9]+)/';
        preg_match($pattern, $url, $matches);
    }

    // If matches are found, return them
    if (isset($matches[1]) && isset($matches[2])) {
        return [
            'latitude' => $matches[1],
            'longitude' => $matches[2],
        ];
    }

    return null; // Return null if no coordinates are found
}



function UploadFilesWithoutReal($path, $image, $model, $request)
{

    $thumbnail = $request;
    $destinationPath = $path;
    $filename = time() . '.' . $thumbnail->getClientOriginalExtension();
    $thumbnail->move($destinationPath, $filename);

    $model->$image = url($path . '/' . $filename);

    $model->save();
}
function UploadFilesIM($path, $image, $model, $request)
{

    // dd($request);

    $imagePaths = [];
    $thumbnail = $request;
    $destinationPath = $path;
    foreach ($thumbnail as $file) {
        if ($file->isValid()) {
            $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $imagePaths[] = url($path . '/' . $filename); // Save relative path
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
    $departmentName = $user->department != null ? ($user->department->name != null ? $user->department->name : 'القسم الرئيسي') : '';

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
    $departmentId = auth()->user()->department_id; // Or however you determine the department ID
    if (auth()->user()->rule_id == 2) {

        return User::all();
    } else {
        return User::where('users.department_id', $departmentId)
            ->where('users.id', '<>', auth()->user()->id)->get();
    }
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



function getMonthNames()
{
    // Set locale to Arabic
    App::setLocale('ar'); // Set locale to Arabic
    setlocale(LC_TIME, 'ar_AE.utf8'); // Change locale to Arabic

    // Retrieve Arabic month names
    $arabicMonths = [];
    for ($month = 1; $month <= 12; $month++) {
        $arabicMonths[] = Carbon::create()->month($month)->translatedFormat('F'); // Use translatedFormat
    }

    return $arabicMonths;
}

function getListOfYears($startYear = 2000)
{
    $currentYear = Carbon::now()->year; // Get the current year
    return range($startYear, $currentYear); // Generate a range of years
}

############################################## Vacation #######################################################################
function CheckStartVacationDate($id)
{
    $EmployeeVaction =  EmployeeVacation::find($id);
    if ($EmployeeVaction->date_from > date('Y-m-d')) {
        return true;
    }
    return false;
}
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
            if ($employeeVacation->end_data || ($employeeVacation->end_date > $expectedEndDate && $employeeVacation->end_date < $today)) {
                return $finished;
            } else {

                if ($employeeVacation->is_exceeded) {
                    if ($employeeVacation->end_date && $employeeVacation->end_date < $today) {
                        return $finished;
                    } else {

                        return $exceeded;
                    }
                } else {
                    return $finished;
                }
            }
        } else {
            if ($employeeVacation->is_cut) {
                return $finished;
            } else {

                return $current;
            }
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
    $endDate = AddDays($startDate, $daysNumber);
    $today = Carbon::today();
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

    $expectedEndDate = AddDays($startDate, $daysNumber);
    $workStartdDate = AddDays($expectedEndDate, 1);
    return [$expectedEndDate, $workStartdDate];
}
function AddDays($date, $daysNumber)
{
    $startDate = Carbon::parse($date);
    $daysNumber = $daysNumber;

    $next_date = $startDate->copy()->addDays($daysNumber);
    return $next_date->toDateString();
}
function formatTime($time)
{
    $to = Carbon::createFromFormat('H:i:s', $time)->format('h:i A');
    $toDay = str_replace(['AM', 'PM'], ['ص', 'م'], $to);
    return $toDay;
}
function convertToArabicNumerals($number)
{
    $westernArabicNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $easternArabicNumerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    return str_replace($westernArabicNumerals, $easternArabicNumerals, $number);
}
function addInspectorHistory($inspector_id, $group_id, $team_id, $is_working = 1)
{

    $today = date('Y-m-d');
    $inspector_group = new InspectorGroupHistory();
    $inspector_group->inspector_id = $inspector_id;
    $inspector_group->group_id = $group_id;
    $inspector_group->group_team_id = $team_id;
    $inspector_group->date = $today;
    $inspector_group->is_working = $is_working;
    $inspector_group->save();
}
function addGroupHistory($group_id, $sector_id)
{
    $today = date('Y-m-d');
    $group_sector = new GroupSectorHistory();
    $group_sector->group_id = $group_id;
    $group_sector->sector_id = $sector_id;
    $group_sector->date = $today;
    $group_sector->save();
}
function getTokenDevice($inspector_id)
{
    $user_id = Inspector::find($inspector_id)->user_id;
    $device_token = User::find($user_id)->device_token;
    return $device_token;
}
if (!function_exists('send_push_notification')) {
    /* function send_push_notification($mission_id,$token,$title,$message){
        $serverkey = 'AAAAFN778j8:APA91bFt1GglZf07Po-5ccwa8tYHuaIz0ymvDZCeDKJ2bxpaNrj2eM1TbON3_EdkhjkcH9IhKsaTOUv0mHSXHWQ-O2t61J6OwgoBmzoftKS-1uKBzTmwlGs0kkGClVYcP0TTXtFArxIT';// this is a Firebase server key
        // device_token
            $data = array(
                'to' => $token,
                'notification' =>
                        array(
                        'body' => $message,
                        'title' => $title),
                        "data"=> array(
                                "mission_id"=> $mission_id,
                                // "mode"=>"rate",
                                "title"=>$title

                            )
                        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: key='.$serverkey));
        $output = curl_exec ($ch);
        $result=json_decode($output);
        curl_close ($ch);
    } */
    function send_push_notification($mission_id, $usertoken, $title, $message)
    {
        $projectId = "taftesh-74633"; //config('services.fcm.project_id'); # INSERT COPIED PROJECT ID

        $credentialsFilePath = Storage::path('json/file.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $usertoken,
                "notification" => [
                    "title" => $title,
                    "body" => $message,
                ],
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
        $response = curl_exec($ch);
        // dd($response);
        $err = curl_error($ch);
        curl_close($ch);
    }
}


function getNotifications()
{
    $notifications = Notification::with('groups', 'teams')->where('user_id', Auth::user()->id)->where('status', 0)->get();

    if ($notifications) {
        return $notifications;
    }
    return null;
}
