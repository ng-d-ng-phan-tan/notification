<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Ramsey\Uuid\Uuid;

class FcmController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return view('home');
    }

    public function saveToken(Request $request)
    {
        auth()->user()->update(['device_token' => $request->token]);
        return response()->json(['token saved successfully.']);
    }

    public function totalUnreadNotification($user_id)
    {
        $total = Notification::query()
            ->where('is_read', false)
            ->count();
        return response()->json(['total' => $total]);
    }

    public function readNotification($user_id){
        try{
            $uuid = Uuid::fromString($user_id);
            Notification::query()
                ->where('user_id', $uuid)
                ->update(['is_read' => true]);
            return response()->json([
                'message' => 'Notification read successfully',
            ]);
        }catch (\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getNotification(Request $request, $user_id)
    {
        try {
            $limit = $request->limit ?? 10;
            $offset = $request->offset ?? 0;
            $total = Notification::query()
                ->where('user_id', $user_id)
                ->count();
            $notifications = Notification::query()
                ->where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();
            $totalUnread = Notification::query()
                ->where('user_id', $user_id)
                ->where('is_read', false)
                ->count();
            return response()->json([
                'total' => $total,
                'notifications' => $notifications,
                'totalUnread' => $totalUnread,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function sendNotification(Request $request)
    {
        try {
            $firebaseToken = [$request->device_token];

            $SERVER_API_KEY = 'AAAA3yIlcTE:APA91bFRh8pMb1AgsGhEgJSqLiszCTmNTu0pHUclCz8QZ1aTrp4rrSmAu3kH-HNqSm_6ZcgPkc_Y47_FJRSqKmzPsYzFp1fd0KDCakO9uXcZqL8KY5zpQ-hnswdlVD2Tjs3-hJ3SUpjw';

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
            $jsonResponse = json_decode($response, true);

            if ($jsonResponse && isset($jsonResponse['success']) && $jsonResponse['success'] === 1) {
                $notification = Notification::query()->create([
                    'user_id' => $request->user_id,
                    'title' => $request->title,
                    'body' => $request->body,
                    "device_token" => $request->device_token,
                ]);
                return response()->json(['success' => true, 'message' => 'Notification sent successfully.', 'notification' => $notification]);
            } else {
                return response()->json(['success' => false, 'message' => 'Notification sent failed.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

    }

}
