<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Models\ResponseMsg;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Redis;
use Mail;

class MailController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function send(): \Illuminate\Http\JsonResponse
    {
        try {
            $body = request()->all();
            $to = $body['to'];
            $data = $body['data'];
            $subject = $body['subject'];
            $template = $body['template'];
            SendEmail::dispatch($to, $data, $subject, $template);
            $responseMsg = new ResponseMsg(200, 'Mail sent successfully', null);
            return response()->json($responseMsg);
        }catch (Exception $e) {
            $responseMsg = new ResponseMsg(400, $e->getMessage(), null);
            return response()->json($responseMsg);
        }
    }

    public function addTemplate(): \Illuminate\Http\JsonResponse
    {
        try {
            $body = request()->all();
            $templateName = $body['template-name'];
            $file = $body['file'];
            $fileExtension = $file->getClientOriginalExtension();
            if (substr($fileExtension, -10) === '.blade.php') {
                return response()->json([
                    'message' => 'File extension must be blade.php',
                ], 400);
            }
            $templatePath = resource_path('views/emails/' . $templateName . '.blade.php');
            if (file_exists($templatePath)) {
                return response()->json([
                    'message' => 'Template already exist',
                ], 400);
            }
            $file->move(resource_path('views/emails'), $templateName . '.' .'blade.php');
            return response()->json([
                'message' => 'Create Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function testCallApi(): JsonResponse
    {
        $body = request()->all();
        Redis::publish('send-mail', json_encode($body));
        return response()->json([
            'message' => 'success',
        ], 200);
    }
}
