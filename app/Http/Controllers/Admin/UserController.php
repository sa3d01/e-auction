<?php

namespace App\Http\Controllers\Admin;

use App\AuctionItem;
use App\AuctionUser;
use App\Item;
use App\Notification;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;

class UserController extends MasterController
{
    public function __construct(User $model)
    {
        $this->model = $model;
        $this->route = 'user';
        parent::__construct();
    }
    public function validation_func($method, $id = null)
    {
        if ($method == 1)
            return ['name' => 'required', 'phone' => 'required|unique:users|max:10|regex:/(05)[0-9]{8}/', 'email' => 'required|unique:users|email|max:50', 'image' => 'mimes:png,jpg,jpeg', 'password' => 'required|min:6'];
        return ['name' => 'required', 'phone' => 'required|regex:/(05)[0-9]{8}/|max:10|unique:users,mobile,' . $id, 'email' => 'required|email|max:50|unique:users,email,' . $id, 'image' => 'mimes:png,jpg,jpeg'];
    }
    public function validation_msg()
    {
        return array(
            'unique' => ' مسجل بالفعل :attribute هذا الـ',
            'required' => ':attribute يجب ادخال الـ',
            'max' =>' يجب أﻻ تزيد قيمته عن :max عناصر :attribute',
            'min' =>' يجب أﻻ تقل قيمته عن :min عناصر :attribute',
            'email'=>'يرجى التأكد من صحة البريد الالكترونى',
            'regex'=>'تأكد من أن رقم الجوال يبدأ ب05 , ويحتوى على عشرة أرقام',
            'image.mimes' => 'يوجد مشكلة بالصورة',
        );
    }
    public function index()
    {
        $rows = $this->model->latest()->get();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'user',
            'title'=>'قائمة العملاء',
            'index_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', ' الجوال' => 'phone','تاريخ الانضمام'=>'created_at'],
            'status'=>true,
            'image'=>true,
        ]);
    }
    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'user',
            'action'=>'admin.user.store',
            'title'=>'أضافة عميل',
            'create_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'phone'],
            'status'=>true,
            'password'=>true,
            'image'=>true,
        ]);
    }
    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $data['user_type_id']=1;
        $this->model->create($data);
        return redirect()->route('admin.user.index')->with('created');
    }
    public function show($id)
    {
        $row = User::findOrFail($id);
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'user',
            'action'=>'admin.user.update',
            'title'=>'الملف الشخصى',
            'edit_fields'=>['ID' => 'id','الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'phone', 'المستحقات' => 'wallet', 'القوة الشرائية' => 'purchasing_power'],
            'selects'=>[[
                'title'=>'الباقة',
                'name'=>'package',
                'input_name'=>'package_id',
            ]],
            'status'=>true,
            'password'=>true,
            'image'=>true,
            'licence_image'=>true,
            'only_show'=>true,
        ]);
    }
    public function activate($id,Request $request){
        $user=$this->model->find($id);
        if($user->status == 1){
            $user->update(
                [
                    'status'=>0,
                ]
            );
        }else{
            $user->update(
                [
                    'status'=>1,
                ]
            );
        }
        $user->refresh();
        return redirect()->back()->with('updated');
    }

    public function clearWallet($id)
    {
        $user=User::find($id);
        if ($user->wallet>0){
            $note['ar'] = 'تم تحويل مستحقاتك لحسابك البنكي بنجاح ! ';
            $note['en'] = 'Your dues has been wired to your bank account successfully !';
        }else{
            try {
                $paid_auction_items=AuctionItem::where('more_details->status','paid')->get();
                $item_ids=[];
                foreach ($paid_auction_items as $paid_auction_item){
                    $winner=AuctionUser::where(['auction_id'=>$paid_auction_item->auction_id,'item_id'=>$paid_auction_item->item_id])->latest()->value('user_id');
                    if ($winner==$user->id){
                        $item_ids[]=$paid_auction_item->item_id;
                    }
                }
                $paid_items=Item::whereIn('id',$item_ids)->latest()->get();
                foreach ($paid_items as $item){
                    $auction_user=AuctionUser::where(['item_id'=>$item->id,'user_id'=>$user->id])->latest()->first();
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
        }
        $user->update([
            'wallet'=>0
        ]);
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

        $user->refresh();
        return redirect()->back()->with('updated');
    }
}
