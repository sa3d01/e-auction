<?php

namespace App\Http\Controllers\Admin;

use App\Notification;
use App\Transfer;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;

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
        $rows = $this->model->where('type','refund_wallet')->orWhere('type','refund_purchasing_power')->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'refund',
            'title'=>'قائمة طلبات استرداد المستحقات',
            'index_fields'=>['النوع' => 'type','المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
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
        if ($row->status==0){
            $row->update(
                [
                    'status'=>1,
                ]
            );
            $user=User::find($row->user_id);
            $note['ar'] = 'تم تحويل مستحقاتك لحسابك البنكي بنجاح ! ';
            $note['en'] = 'Your dues has been wired to your bank account successfully !';
            if ($row->type=='refund_purchasing_power'){
                $note['ar'] = 'تم تحويل عربونك لحسابك البنكي بنجاح ! ';
                $note['en'] = 'Your deposit has been wired to your bank account successfully !';
                $user->update([
                    'purchasing_power'=>0
                ]);
            }elseif ($row->type=='refund_wallet'){
                $note['ar'] = 'تم تحويل مستحقاتك لحسابك البنكي بنجاح ! ';
                $note['en'] = 'Your dues has been wired to your bank account successfully !';
                $user->update([
                    'wallet'=>0
                ]);
            }
            $this->notify($user, $note);
        }
        $row->refresh();

        $rows = $this->model->where('type','refund_wallet')->orWhere('type','refund_purchasing_power')->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'refund',
            'title'=>'قائمة استرداد المستحقات',
            'index_fields'=>['النوع' => 'type','المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
            'status'=>true,
        ])->with('updated');
    }
    public function reject($id,Request $request){
        $row=$this->model->find($id);
        if ($row->status==0){
            $row->update(
                [
                    'status'=>-1,
                ]
            );
            $user=User::find($row->user_id);
            $note['ar']='تم رفض طلب استرداد مستحقاتك للسبب التالى : '.$request['reject_reason'];
            $note['en'] = 'your refund dues is rejected from admin  ..'.$request['reject_reason'];
            if ($row->type=='refund_purchasing_power'){
                $note['ar']='تم رفض طلب استرداد عربونك للسبب التالى : '.$request['reject_reason'];
                $note['en'] = 'your refund deposit is rejected from admin  ..'.$request['reject_reason'];
            }elseif ($row->type=='refund_wallet'){
                $note['ar']='تم رفض طلب استرداد مستحقاتك للسبب التالى : '.$request['reject_reason'];
                $note['en'] = 'your refund dues is rejected from admin  ..'.$request['reject_reason'];
            }

            $this->notify($user, $note);
        }
        $row->refresh();
        $rows = $this->model->where('type','refund_wallet')->orWhere('type','refund_purchasing_power')->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'refund',
            'title'=>'قائمة استرداد المستحقات',
            'index_fields'=>['النوع' => 'type','المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
            'status'=>true,
        ])->with('updated');
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
            'notification' => array('title' => 'رسالة ادارية','body' => $note['ar'], 'sound' => 'default'),
            'data' => [
                'title' => 'رسالة ادارية',
                'body' => $note['ar'],
                'type' => 'transfer',
                'db'=>true,
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($user->device['id'])
            ->send()
            ->getFeedback();
    }
}
