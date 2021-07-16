<?php

namespace App\Http\Controllers\Admin;

use App\AuctionItem;
use App\AuctionUser;
use App\Item;
use App\Notification;
use App\Transfer;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;

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
        $rows=$this->model->where('purchasing_type','bank')->where(function ($query){
            $query->where('type','wallet')->orWhere('type','purchasing_power');
        })->latest()->get();
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
        if ($row->status==0){
            $row->update(
                [
                    'status'=>1,
                ]
            );
            if ($row->type=='wallet'){
                try {
                    $paid_auction_items=AuctionItem::where('more_details->status','paid')->get();
                    $item_ids=[];
                    foreach ($paid_auction_items as $paid_auction_item){
                        $winner=AuctionUser::where(['auction_id'=>$paid_auction_item->auction_id,'item_id'=>$paid_auction_item->item_id])->latest()->value('user_id');
                        if ($winner==$row->user_id){
                            $item_ids[]=$paid_auction_item->item_id;
                        }
                    }
                    $paid_items=Item::whereIn('id',$item_ids)->latest()->get();
                    foreach ($paid_items as $item){
                        $auction_user=AuctionUser::where(['item_id'=>$item->id,'user_id'=>$row->user_id])->latest()->first();
                        $auction_item=AuctionItem::where('item_id',$item->id)->where('more_details->status','paid')->latest()->first();
                        $auction_user->update([
                            'more_details'=>[
                                'status'=>'paid',
                                'total_amount'=>$this->totalAmount($auction_item),
                                'paid'=>$this->totalAmount($auction_item),
                                'remain'=>0
                            ]
                        ]);
                        $auction_item->update([
                            'more_details' => [
                                'status'=>'delivered',
                                'pay_type' => $auction_item->more_details['pay_type']
                            ]
                        ]);
                    }
                }catch (\Exception $e){

                }
                $note['ar'] = 'تم إستلام المستحقات الخاصة بكم ! . شكرا لثقتكم ';
                $note['en'] = 'Your outstanding balance has been cleared !. Thanks ';
                $this->editWallet($row->user,$row->money);
            }else{
                $row->user->update(['purchasing_power' =>$row->user->purchasing_power+ $row->money]);
                $note['ar'] = 'تم قبول طلب شحن العربون بنجاح ! . يمكنكم البدأ بالمزايدة';
                $note['en'] = 'Your deposit has been approved !. You can start bidding';
            }

            $this->notify($row->user, $note);
        }
        $row->refresh();
        $rows=$this->model->where('purchasing_type','bank')->where(function ($query){
            $query->where('type','wallet')->orWhere('type','purchasing_power');
        })->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'transfer',
            'title'=>'قائمة الحوالات',
            'index_fields'=>['المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
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
            if ($row->type=='purchasing_power'){
                $note['ar']='تم رفض طلب شحن العربون وذلك للأسباب التالية: '.$request['reject_reason'];
                $note['en'] = 'Your request to put a deposit has been declined for this reason: '.$request['reject_reason'];
            }else{
                $note['ar']='تم رفض طلب دفع المستحقات للإسباب التالية: '.$request['reject_reason'];
                $note['en'] = 'your request to pay your outstanding balance is declined for this reason: '.$request['reject_reason'];
            }
            $this->notify($user, $note);
        }
        $row->refresh();
        $rows=$this->model->where('purchasing_type','bank')->where(function ($query){
            $query->where('type','wallet')->orWhere('type','purchasing_power');
        })->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'transfer',
            'title'=>'قائمة الحوالات',
            'index_fields'=>['المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
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
            'notification' => array('title' => $note['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $note['ar'],
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
