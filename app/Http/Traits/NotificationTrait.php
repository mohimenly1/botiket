<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

trait NotificationTrait
{

    public function index(Request $request)
    {
        $notifications = Notification::where('to_id', $request->user()->id)->latest()->get();

        return response()->json(['data' => $notifications], 200);
    }

    public function changeRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read = 1;
        $notification->save();
    }

    public static function send($sender_id, $receiver_id, $title, $message = "", $data_id, $notifyType)
    {
        //  dd($sender_id, $receiver_id, $title, $message , $data_id, $notifyType);
        foreach ($receiver_id as $user) {
            $notification = new Notification();
            $notification->from_id = $sender_id;
            $notification->to_id = $user;
            $notification->title = $title;
            $notification->body = $message;
            $notification->data_id = $data_id;
            $notification->type = $notifyType;
            $notification->save();
            foreach (User::whereId($user)->first()->fcm()->pluck('fcm_token') as $record) {
                // dd($record);
                $data = json_encode([
                    'to' => $record,
                    'data' => [
                        'data_id' => $notification->data_id,
                        'type' => $notification->type,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                    'notification' => [
                        'title' => $title,
                        'body' => $message
                    ],
                ]);

                $url = 'https://fcm.googleapis.com/fcm/send';
                $server_key = 'AAAAvLRNEJc:APA91bGS4Wper8CtFueYnUfVKK0Nfde1cxkKWRfSIedr1ASfvae-i57coWcnGGizO6qSy8HXZ9-B_Fk0l1WuZ-bW2CftyGiPGAlFOiZgm_QEIsF_raqujQaeyT2nvpPdwd6_816-BfNk';

                $headers = [
                    'Content-Type:application/json',
                    'Authorization:key=' . $server_key
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                // curl_exec($ch);
                // curl_close($ch);
                // Execute post
                $result = curl_exec($ch);

                if ($result === false) {
                    die('Curl failed: ' . curl_error($ch));
                }

                // Close connection
                curl_close($ch);

                // FCM response
                //  dd($result);
            }
        }
    }
}
