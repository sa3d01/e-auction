<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Notification;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use function request;

class NotificationController extends MasterController
{
    protected $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
        parent::__construct();
    }


    public function show($id)
    {
        if (!$this->model->find($id))
            return $this->sendError('not found');
        $single = $this->model->find($id);
        $single->update([
            'read' => 'true'
        ]);
        return $this->sendResponse(NotificationResource::make($this->model->find($id)));
    }

    public function index()
    {
        $notifies = new NotificationCollection($this->model->where('receiver_id', request()->user()->id)->latest()->get());
        $unread = $this->model->where('receiver_id', request()->user()->id)->where('read', 'false')->count();
        return $this->sendResponse(['data' => $notifies, 'unread' => $unread]);
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
        $receivers=User::all();
        $title='رسالة إدارية';
        $note=$request['note'];
        $token_receivers=[];
        foreach ($receivers as $receiver){
            if ($receiver->device['id'] !='null'){
                $token_receivers[]=$receiver->device['id'];
            }
        }
        $push = new PushNotification('fcm');
        $push->setMessage([
            'notification' => array('title'=>$title, 'sound' => 'default'),
            'data' => [
                'title' => $title,
                'body' => $note,
                'status' => 'admin',
                'type'=>'admin',
            ],
            'priority' => 'high',
        ])
            ->setDevicesToken($token_receivers)
            ->send();
        return redirect()->back()->with('created');
    }


}
