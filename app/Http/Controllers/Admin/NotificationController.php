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
        $rows=$this->model->where('admin_notify_type',$admin_notify_type)->where('receivers','!=',null)->latest()->get();
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
            'only_show'=>true,
        ]);
    }

    public function store(Request $request){
        $admin_notify_type=$request['admin_notify_type'];
        $receivers=User::all();
        $receivers_ids=$receivers->pluck('id');
        $title='رسالة إدارية';
        $note['ar']=$request['note_ar'];
        $token_receivers=[];
        foreach ($receivers as $receiver){
            if ($receiver->device['id'] !='null'){
                $token_receivers[]=$receiver->device['id'];
            }
        }
        $push = new PushNotification('fcm');
        $push->setMessage([
            'notification' => array('title'=>$title,'body' => $note['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title,
                'body' => $note['ar'],
                'status' => 'admin',
                'type'=>'admin',
                'db'=>true,
            ],
            'priority' => 'high',
        ])
            ->setDevicesToken($token_receivers)
            ->send();

        Notification::create([
            'receivers'=>$receivers_ids,
            'admin_notify_type'=>$admin_notify_type,
            'title'=>$title,
            'note'=>$note,
            'type'=>'admin',
        ]);
        return redirect()->back()->with('created');
    }

}
