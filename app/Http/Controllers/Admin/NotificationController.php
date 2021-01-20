<?php

namespace App\Http\Controllers\Admin;

use App\Notification;
use App\User;
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
        $receivers=User::all();
        $receivers_ids=$receivers->pluck('id');
        $title['ar']='رسالة إدارية';
        $title['en']='admin message';
        $note['ar']=$request['note'];
        $note['en']=$request['note'];
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title'=>$title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $note['ar'],
                'status' => 'admin',
                'type'=>'admin',
            ],
            'priority' => 'high',
        ];
        foreach ($receivers as $receiver){
            $push->setMessage($msg)
                ->setDevicesToken($receiver->device['id'])
                ->send();
        }
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
