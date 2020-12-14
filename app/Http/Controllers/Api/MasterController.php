<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Notification;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class MasterController extends Controller
{
    protected $model;
    protected $auth_key;
    public function __construct()
    {
        parent::__construct();
    }
    public function sendResponse($result)
    {
        $response = [
            'status' => 200,
            'data' => $result,
        ];
        return response()->json($response, 200);
    }
    public function sendError($error, $code = 400)
    {
        $response = [
            'status' => $code,
            'message' => $error,
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),$this->validation_rules(1),$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data=$request->all();
        try {
            $data['user_id']=auth()->user()->id;
        }catch (UserNotDefinedException $e){

        }
        $this->model->create($data);
        return $this->sendResponse('تم الانشاء بنجاح');
    }
    public function notify($order,$sender,$receiver,$title){
        $receiver->device['type'] =='IOS'? $fcm_notification=array('title'=>$title, 'sound' => 'default') : $fcm_notification=null;
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => $fcm_notification,
            'data' => [
                'title' => $title,
                'body' => $title,
                'status' => $order->status,
                'type'=>'order',
                'order'=>new OrderResource($order),
                'id'=>$order->id
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($receiver->device['id'])
            ->send();
        $notification=new Notification();
        $notification->type='app';
        $notification->sender_id=$sender->id;
        $notification->receiver_id=$receiver->id;
        $notification->order_id=$order->id;
        $notification->title=$title;
        $notification->note=$title;
        $notification->save();
    }

}
