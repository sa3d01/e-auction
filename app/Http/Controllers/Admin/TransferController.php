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

class TransferController extends MasterController
{
    public function __construct(Transfer $model)
    {
        $this->model = $model;
        $this->route = 'transfer';
        parent::__construct();
    }


    public function index()
    {
        $rows = $this->model->where(['purchasing_type'=>'bank','type'=>'buy_item'])->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'transfer',
            'title'=>'قائمة الحوالات',
            'index_fields'=>['المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
            'status'=>true,
        ]);
    }

    public function show($id)
    {
        $row = Transfer::findOrFail($id);
        return View('dashboard.transfer.show', [
            'row' => $row,
            'type'=>'transfer',
            'action'=>'admin.transfer.update',
            'title'=>'حوالة بنكية',
        ]);
    }

    public function accept($id){
        $row=$this->model->find($id);
        $row->update(
            [
                'status'=>1,
            ]
        );
        $auction_item=AuctionItem::where('item_id',$row->more_details['item_id'])->latest()->first();
        $auction_item->update(
            [
                'more_details'=>[
                    'status'=>'delivered'
                ]
            ]
        );
        $user=User::find($row->user_id);
        $note['ar'] = 'تم الموافقة على تحويلك البنكى بنجاح :)';
        $note['en'] = 'your transfer is accepted from admin  ..';
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
        $note['ar']='تم رفض تحويلك البنكى للسبب التالى : '.$request['reject_reason'];
        $note['en'] = 'your transfer is rejected from admin  ..'.$request['reject_reason'];
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
