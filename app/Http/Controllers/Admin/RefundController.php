<?php

namespace App\Http\Controllers\Admin;

use App\Ask;
use App\AuctionItem;
use App\DropDown;
use App\Notification;
use App\Transfer;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends MasterController
{
    public function __construct(Transfer $model)
    {
        $this->model = $model;
        $this->route = 'transfer';
        parent::__construct();
    }


    public function index()
    {
        $rows = $this->model->where(['purchasing_type'=>'bank','type'=>'refund_credit'])->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'refund',
            'title'=>'قائمة استرداد المستحقات',
            'index_fields'=>['المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
            'status'=>true,
        ]);
    }

    public function show($id)
    {
        $row = Transfer::findOrFail($id);
        return View('dashboard.transfer.show', [
            'row' => $row,
            'type'=>'refund',
            'action'=>'admin.transfer.update',
            'title'=>'طلب استرداد مستحقات',
        ]);
    }

    public function accept($id){
        $row=$this->model->find($id);
        $row->update(
            [
                'status'=>1,
            ]
        );
        $user=User::find($row->user_id);
        $note['ar'] = 'تم الموافقة على طلب استرداد مستحقاتك بنجاح :)';
        $note['en'] = 'your refund order is accepted from admin  ..';
        $this->notify($user, $note);
        $row->refresh();
        return redirect()->back()->with('updated');
    }
    public function reject($id,Request $request){
        $row=$this->model->find($id);
        $row->update(
            [
                'status'=>-1,
            ]
        );
        $user=User::find($row->user_id);
        $note['ar']='تم رفض طلب استرداد مستحقاتك للسبب التالى : '.$request['reject_reason'];
        $note['en'] = 'your refund order is rejected from admin  ..'.$request['reject_reason'];
        $this->notify($user, $note);
        $row->refresh();
        return redirect()->back()->with('updated');
    }
    function notify($user, $note)
    {
        Notification::create([
            'receiver_id' => $user->id,
            'title' => $note,
            'note' => $note,
        ]);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $note['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $note['ar'],
                'body' => $note['ar'],
                'type' => 'transfer',
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($user->device['id'])
            ->send()
            ->getFeedback();
    }
}
