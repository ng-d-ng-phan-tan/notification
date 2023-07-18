<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Models\ResponseMsg;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mail;

class MailController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function send(): \Illuminate\Http\JsonResponse
    {
        $body = request()->all();
        $to = $body['to'];
        $data = $body['data'];
        $subject = $body['subject'];
        $template = $body['template'];
        SendEmail::dispatch($to, $data, $subject, $template);
        $responseMsg = new ResponseMsg(200, 'Mail sent successfully',null);
        return response()->json($responseMsg);
    }

    public function addTemplate():\Illuminate\Http\JsonResponse
    {
        $body = request()->all();
        $to = $body['to'];
        $data = $body['data'];
        $otp = $data['otp'];
        $content = $body['content'];
        $subject = $body['subject'];
        Mail::send('emails.test', ['otp' => $otp], function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
        return response()->json([
            'message' => 'Mail sent successfully',
        ], 200);
    }
}
