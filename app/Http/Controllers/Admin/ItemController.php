<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\ItemResource;
use App\Http\Resources\OrderResource;
use App\Item;
use App\Notification;
use App\Report;
use App\Setting;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends MasterController
{
    public function __construct(Item $model)
    {
        $this->model = $model;
        $this->route = 'item';
        parent::__construct();
    }

    public function items($status)
    {
        $rows=$this->model->where('status',$status)->latest()->get();
        if ($status=='accepted'){
            return View('dashboard.item.index', [
                'rows' => $rows,
                'status'=>$status,
                'type'=>'item',
                'title'=>'قائمة السلع المطلوب اعدادها',
                'index_fields'=>['الرقم التسلسلى' => 'id','العنوان'=>'name'],
                'selects'=>[
                    [
                        'name'=>'user',
                        'title'=>'المستخدم'
                    ],
                    [
                        'name'=>'auction_type',
                        'title'=>'نوع المزايدة'
                    ],
                ],
            ]);
        }else{
            return View('dashboard.item.index', [
                'rows' => $rows,
                'status'=>$status,
                'type'=>'item',
                'title'=>'قائمة السلع',
                'index_fields'=>['الرقم التسلسلى' => 'id','العنوان'=>'name','تاريخ الطلب'=>'created_at'],
                'selects'=>[
                    [
                        'name'=>'user',
                        'title'=>'المستخدم'
                    ],
                    [
                        'name'=>'auction_type',
                        'title'=>'نوع المزايدة'
                    ],
                ],
            ]);
        }

    }

    public function reports($item_id){
        return View('dashboard.report.index', [
                'rows' => Report::where('item_id',$item_id)->latest()->get(),
                'item_id'=>$item_id,
                'type'=>'report',
                'title'=>'قائمة تقارير الفحص',
                'index_fields'=>['الرقم التسلسلى' => 'id','العنوان'=>'title','الوصف'=>'note','الصور'=>'images','السعر'=>'price'],
            ]
        );
    }
    public function auction_price($item_id,Request $request){
        $item=$this->model->find($item_id);
        $auction_price=$request['auction_price'];
        $item->update([
            'auction_price'=>$auction_price
        ]);
        return redirect()->back()->with('updated');
    }

    public function show($id)
    {
        $row=$this->model->findOrFail($id);
        $fields=[
            'الرقم التسلسلى' => 'id',
            'العنوان'=>'name',
            'تاريخ الطلب'=>'created_at',
            'صور السلعة'=>'images',
            'عدد السندرات'=>'sunder_count',
            'الممشى'=>'kms_count',
            'صورة الاستمارة'=>'paper_image',
            'السعر'=>'price',
            'نوع الشحن'=>'shipping_by',
        ];
        return View('dashboard.item.show', [
            'row' => $row,
            'type'=>'item',
            'action'=>'admin.item.update',
            'title'=>'بيانات السلعة',
            'show_fields'=>$fields,
            'selects'=>[
                [
                    'name'=>'user',
                    'title'=>'المستخدم',
                    'route'=>route('admin.user.show',[$row->user_id])
                ],
                [
                    'name'=>'auction_type',
                    'title'=>'نوع المزايدة',
                ],
                [
                    'name'=>'mark',
                    'title'=>'نوع المركبة',
                ],
                [
                    'name'=>'model',
                    'title'=>'موديل المركبة',
                ],
                [
                    'name'=>'color',
                    'title'=>'لون المركبة',
                ],
                [
                    'name'=>'item_status',
                    'title'=>'حالة المركبة',
                ],
                [
                    'name'=>'fetes',
                    'title'=>'نوع ناقل الحركة',
                ],
                [
                    'name'=>'scan_status',
                    'title'=>'حالة الفحص',
                ],
                [
                    'name'=>'paper_status',
                    'title'=>'حالة الاستمارة',
                ],
                [
                    'name'=>'city',
                    'title'=>'المدينة',
                ],
            ],
            'location'=>true,
            'only_show'=>true,
        ]);
    }

    public function reject($id,Request $request){
        $item=$this->model->find($id);
        $reject_reason=$request['reject_reason'];
        $history[date('Y-m-d')]['rejected']=[
            'time'=>date('H:i:s'),
            'admin_id'=>Auth::user()->id,
            'reason'=>$reject_reason,
        ];
        $item->update([
            'status'=>'rejected',
            'more_details'=>[
                'history'=>$history,
            ],
        ]);
        $note['ar']='تم رفض اضافة منتجك من قبل الادارة للسبب التالى ..'.$reject_reason;
        $note['en']='your added item is rejected from admin for that reason ..'.$reject_reason;
        $this->itemStatusNotify($item,$note);
        $item->refresh();
        return redirect()->back()->with('updated');
    }

    public function accept($id,Request $request){
        $item=$this->model->find($id);
        $history[date('Y-m-d')]['accepted']=[
            'time'=>date('H:i:s'),
            'admin_id'=>Auth::user()->id,
        ];
        $item->update([
            'status'=>'accepted',
            'more_details'=>[
                'history'=>$history,
            ],
        ]);
        if ($item->pay_status==1){
            $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..';
            $note['en']='your added item is accepted from admin  ..';
        }else{
            $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..ويرجى شحن محفظتك قريبا لتحصيل ضريبة الاضافة لمزاد';
            $note['en']='your added item is accepted from admin..please charge your wallet to add to auction';
        }
        $this->itemStatusNotify($item,$note);
        $item->refresh();
        return redirect()->back()->with('updated');
    }

    function itemStatusNotify($item,$note){
        Notification::create([
            'receiver_id'=>$item->user_id,
            'item_id'=>$item->id,
            'title'=>$note,
            'note'=>$note,
        ]);
        $item->user->device['type'] =='IOS'? $fcm_notification=array('title'=>$note, 'sound' => 'default') : $fcm_notification=null;
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => $fcm_notification,
            'data' => [
                'title' => $note,
                'body' => $note,
                'type'=>'item',
                'item'=>new ItemResource($item),
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($item->user->device['id'])
            ->send();
    }
}