<?php

namespace App\Http\Controllers;

use App\Models\User;
use Google\Service\ServiceControl\Auth;
use Google\Service\Storage;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Google\Client as GoogleClient;


class NotificationSendController extends Controller
{
    protected $notification;
    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }

    public function setToken(Request $request)
    {
        $token = $request->input('fcm_token');
        $request->user()->update([
            'fcm_token' => $token
        ]); //Get the currrently logged in user and set their token
        return response()->json([
            'message' => 'Successfully Updated FCM Token'
        ]);
    }
    public function updateDeviceToken(Request $request)
    {
        //dd( $request->token);
        Auth::user()->fcm_token =  $request->token;

        Auth::user()->save();

        return response()->json(['Token successfully stored.']);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['message' => 'Device token updated successfully']);
    }

    // public function sendFcmNotification(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'title' => 'required|string',
    //         'body' => 'required|string',
    //     ]);

    //     $user = User::find($request->user_id);
    //     $fcm = $user->fcm_token;

    //     if (!$fcm) {
    //         return response()->json(['message' => 'User does not have a device token'], 400);
    //     }

    //     $title = $request->title;
    //     $description = $request->body;
    //     $projectId = config('1:930391301074:web:45a7ad03354d8d069dc60b'); # INSERT COPIED PROJECT ID

    //     $credentialsFilePath = Storage::path('app/json/file.json');
    //     $client = new GoogleClient();
    //     $client->setAuthConfig($credentialsFilePath);
    //     $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    //     $client->refreshTokenWithAssertion();
    //     $token = $client->getAccessToken();

    //     $access_token = $token['access_token'];

    //     $headers = [
    //         "Authorization: Bearer $access_token",
    //         'Content-Type: application/json'
    //     ];

    //     $data = [
    //         "message" => [
    //             "token" => $fcm,
    //             "notification" => [
    //                 "title" => $title,
    //                 "body" => $description,
    //             ],
    //         ]
    //     ];
    //     $payload = json_encode($data);

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    //     curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
    //     $response = curl_exec($ch);
    //     $err = curl_error($ch);
    //     curl_close($ch);

    //     if ($err) {
    //         return response()->json([
    //             'message' => 'Curl Error: ' . $err
    //         ], 500);
    //     } else {
    //         return response()->json([
    //             'message' => 'Notification has been sent',
    //             'response' => json_decode($response, true)
    //         ]);
    //     }
    // }
}
