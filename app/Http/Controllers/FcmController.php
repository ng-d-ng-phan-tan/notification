<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class FcmController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return view('home');
    }

    public function saveToken(Request $request)
    {
        auth()->user()->update(['device_token'=>$request->token]);
        return response()->json(['token saved successfully.']);
    }

    public function sendNotification(Request $request)
    {
        $firebaseToken = ["dhROXxwboDeuZBbMJzzCiO:APA91bEAvFNGcGVf0sQ79wxvjp0Ge-H8IeZm4eMx8eAxkYL6W3YH7TRN2x_HOVm4lW8qx8i8hEuNxyuMEi81l29TI0rSQ-Ld9HcCfxL0pLqcrmf8Oe3nGUgzeKVsW4HBgeOJzFscupcx"];

        $SERVER_API_KEY = 'AAAAliAwlnU:APA91bGoDb7nI_YyeJRFRJ3OFW7LeCBx1s1IlZEDzHwq8V_soqDSUGlxfR-7UgbkAlmLT1cX2Ck8ISFp7K_3p-56BhcuCy0999PhGA02QnyJJ0kJobHIcuX0nzyUPDLjo6F8DIUBnH4H';

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
                "content_available" => true,
                "priority" => "high",
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        dd($response);
    }

}
