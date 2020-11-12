<?php

namespace App\Http\Controllers\Admin;

use App\Notification;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;

class NotificationController extends MasterController
{
    public function __construct(Notification $model)
    {
        $this->model = $model;
        $this->route = 'notification';

        parent::__construct();
    }

    public function notifications($admin_notify_type)
    {
        $rows=$this->model->where('admin_notify_type',$admin_notify_type)->latest()->get();
        $collection = collect($rows);
        $rows = $collection->unique('created_at');
        $rows->values()->all();

        return View('dashboard.notification.index', [
            'rows' => $rows,
            'action'=>'admin.notification.store',
            'type'=>'notification',
            'admin_notify_type'=>$admin_notify_type,
            'title'=>$this->model->nameForShow($admin_notify_type),
            'index_fields'=>['نص الاشعار'=>'note','تاريخ الارسال'=>'created_at'],
            'create_fields'=>['نص الاشعار' => 'note'],
            'create_alert'=>'يمكنك ارسال رسالة لمستخدم واحد من خﻻل صفحته الشخصية',
            'only_show'=>true,
        ]);
    }

    public function store(Request $request){
        $admin_notify_type=$request['admin_notify_type'];
        if ($admin_notify_type=='user'){
            $receivers=User::where('user_type_id',1)->get();
        }elseif ($admin_notify_type=='provider'){
            $receivers=User::where('user_type_id',3)->orWhere('user_type_id',4)->get();
        }else{
            $receivers=User::all();
        }
        $receivers_ids=$receivers->pluck('id');
        $title='رسالة إدارية';
        $note=$request['note'];
        $push = new PushNotification('fcm');
        $android_msg = [
            'notification' => null,
            'data' => [
                'title' => $title,
                'body' => $note,
                'status' => 'admin',
                'type'=>'admin',
            ],
            'priority' => 'high',
        ];
        $ios_msg = [
            'notification' => array('title'=>$title, 'sound' => 'default'),
            'data' => [
                'title' => $title,
                'body' => $note,
                'status' => 'admin',
                'type'=>'admin',
            ],
            'priority' => 'high',
        ];
        $android_receivers=[];
        $ios_receivers=[];
        foreach ($receivers as $receiver){
            if(array_key_exists("type",(array)$receiver->device)){
                if ($receiver->device['type'] =='IOS'){
                    $ios_receivers[]=$receiver->device['id'];
                }else{
                    $android_receivers[]=$receiver->device['id'];
                }
            }
        }
        $push->setMessage($ios_msg)
            ->setDevicesToken($ios_receivers)
            ->send();
        $push->setMessage($android_msg)
            ->setDevicesToken($android_receivers)
            ->send();
        $notification=new Notification();
        $notification->type='admin';
        $notification->receivers=$receivers_ids;
        $notification->title=$title;
        $notification->note=$note;
        $notification->admin_notify_type=$admin_notify_type;
        $notification->save();
        return redirect()->back()->with('created');
    }
}
