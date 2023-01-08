<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTraits;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\Topics;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use LaravelFCM\Facades\FCM as FacadesFCM;
use App\Http\Traits\NotificationTrait;

class NotificationController extends Controller
{
    use ResponseTraits,NotificationTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // try {
        $data = Notification::where('to_id', auth()->id())->orderBy('id', 'desc')->get();
        return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), $data, 200);
        // } catch (\Exception $e) {
        //     return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        // }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = json_encode([
                "to" => '/topics/news',
                'data' => [
                    'data_id' => $request->data_id,
                    'type' => $request->type,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
                'notification' => [
                    'title' => $request->title,
                    'body' => $request->body,
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
           
            $result = curl_exec($ch);

            if ($result === false) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);
            return $this->prepare_response(null, __('auth.data returned') . __('auth.successfully'), 'done', 200);
        } catch (\Exception $e) {
            return $this->prepare_response([$e], __('auth.Something went wrong'), null, 400);
        }
       
        
    }




    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Notification $notification)
    {

        return ($notification->update(['is_read' => 1]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
