<?php

namespace App\Http\Controllers\Admin;

use App\AuctionItem;
use App\DropDown;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
use App\Package;
use App\Report;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemController extends MasterController
{
    public function __construct(Item $model)
    {
        $this->model = $model;
        $this->route = 'item';
        parent::__construct();
    }

    public function create()
    {
        return View('dashboard.item.create', [
            'type' => 'item',
            'action' => 'admin.item.store',
            'title' => 'أضافة سلعة',
            'create_fields'=>['صور المركبة'=>'images','عدد السلندرات'=>'sunder_count','الممشى'=>'kms_count'],
            'images'=>true,
            'paper_image'=>true,
        ]);
    }

    public function store(Request $request)
    {
        $user = User::where('email','admin@admin.com')->first();
        if (!$user){
            $package=Package::where('price','!=',0)->latest()->first();
            $user=User::create([
                'name'=>'admin',
                'email'=>'admin@admin.com',
                'email_verified_at'=>Carbon::now(),
                'package_id'=>$package->id,
                'purchasing_power'=>100000,
                'status'=>1,
            ]);
        }
        $data=$request->all();
        $data['user_id']=$user->id;
        $data['status']='accepted';
        $data['shipping_by']='user';
        $data['pay_status']=1;
        $items_images=[];
        if ($request->input('images')){
            foreach ($request->input('images') as $image){
                $filename=null;
                if (is_file($image)) {
                    if ($image->getSize() > 10000000000){
                        return redirect()->back()->withErrors(['msg', 'حجم الصورة كبير جدا..']);
                    }
                    $filename = Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move('media/images/item/', $filename);
                    $local_name=asset('media/images/item/').'/'.$filename;
                }else {
                    $local_name = $image;
                }
                $items_images[]=$local_name;
            }
            $data['images'] = $items_images;
        }
        $item=$this->model->create($data);
        return redirect()->route('admin.item.status',['status'=>'accepted'])->with('created', 'تمت الاضافة بنجاح');
    }

    public function items($status)
    {
        $rows=$this->model->where('status',$status)->latest()->get();
        if ($status=='accepted'){
            $title='قائمة السلع المطلوب اعدادها';
            $index_fields=['الرقم التسلسلى' => 'id'];
        }else{
            $title='قائمة السلع';
            $index_fields=['الرقم التسلسلى' => 'id','تاريخ الطلب'=>'created_at'];
        }
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>$status,
            'type'=>'item',
            'title'=>$title,
            'index_fields'=>$index_fields,
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

    public function vip_auction_items(){
        $row_ids=AuctionItem::where('vip','true')->pluck('item_id');
        $rows=Item::whereIn('id',$row_ids)->latest()->get();
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>'shown',
            'type'=>'item',
            'title'=>'قائمة السلع المميزة',
            'index_fields'=>['الرقم التسلسلى' => 'id'],
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

    public function uploadImages($item_id,Request $request){
        $item=$this->model->find($item_id);
        $current_images=json_decode($item->imagesArray());
        $images=[];
        if ($request->input('images')){
            foreach ($request->input('images') as $image){
                $filename=null;
                if (is_file($image)) {
                    if ($image->getSize()  > 10000000000){
                        return redirect()->back()->withErrors(['msg', 'حجم الصورة كبير جدا..']);
                    }
                    $filename = Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move('media/images/item/', $filename);
                    $local_name=asset('media/images/item/').'/'.$filename;
                }else {
                    $local_name = $image;
                }
                $images[] = $local_name;
            }
        }
        foreach ($current_images as $old_image){
            $images[]=$old_image;
        }
        $item->update([
            'images'=>$images
        ]);
        return redirect()->back()->with('updated');
    }

    public function update_vip($item_id){
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        if ($auction_item->vip === "true"){
            $auction_item->update([
                'vip'=>'false'
            ]);
        }else{
            $auction_item->update([
                'vip'=>'true'
            ]);
        }
        $auction_item->refresh();
        $auction_item->refresh();
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => null,
            'data' => [
                'title' => '',
                'body' => '',
                'type'=>'new_auction',
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->sendByTopic('new_auction')
            ->send();
        return redirect()->back()->with('updated');
    }

    public function show($id)
    {
        $unread_notifications=Notification::where(['receiver_id'=>null,'item_id'=>$id,'read'=>'false'])->get();
        foreach ($unread_notifications as $unread_notification){
            $unread_notification->update(['read'=>'true']);
        }
        $row=$this->model->findOrFail($id);
        $fields=[
            'الرقم التسلسلى' => 'id',
            'تاريخ الطلب'=>'created_at',
            'صور السلعة'=>'images',
            'عدد السندرات'=>'sunder_count',
            'الممشى'=>'kms_count',
            'صورة الاستمارة'=>'paper_image',
            'السعر'=>'price',
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
        $more_details['history']=$history;
        $add_item_tax=Setting::first()->value('add_item_tax');
        if ($add_item_tax < $item->user->wallet){
            $this->itemTaxPay($item);
        }
        if ($item->pay_status==1){
            $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..';
            $note['en']='your added item is accepted from admin  ..';
        }else{
            $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..ويرجى شحن محفظتك قريبا لتحصيل ضريبة الاضافة لمزاد';
            $note['en']='your added item is accepted from admin..please charge your wallet to add to auction';
        }
        if ($item->shipping_by=='app'){
            $more_details['shipping_price']=$request['shipping_price'];
            if ($request['shipping_price'] < $item->user->wallet){
                $this->walletPay($item->user,$request['shipping_price'],'shipping');
                $more_details['shipping_price_status']='paid';
            }else{
                $more_details['shipping_price_status']='pending';
                $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..ويرجى شحن محفظتك قريبا لتحصيل مستحقات التطبيق المالية';
                $note['en']='your added item is accepted from admin..please charge your wallet ..';
            }
        }
        $item->update([
            'status'=>'accepted',
            'more_details'=>$more_details,
        ]);

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
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $note['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $note['ar'],
                'body' => $note['ar'],
                'type' => 'item',
                'item' => new ItemResource($item),
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($item->user->device['id'])
            ->send();
    }

    function itemTaxPay($item){
        $add_item_tax=Setting::first()->value('add_item_tax');
        $item->update(['pay_status'=>1]);
        $this->walletPay($item->user,$add_item_tax,'add_item_tax');
    }

    function walletPay($user,$price,$type){
        $wallet=$user->wallet-$price;
        $user->update(['wallet'=>$wallet]);
    }

    public function preLiveItems()
    {
        $ids=[];
        foreach (Item::all() as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->latest()->first();
            if ($auction_item){
                if ($auction_item->more_details['status']=='soon'){
                    $ids[]=$item->id;
                }
            }
        }
        $rows=$this->model->whereIn('id',$ids)->latest()->get();
        $title='قائمة السلع فى المزاد قبل المباشر';
        $index_fields=['الرقم التسلسلى' => 'id','تاريخ الطلب'=>'created_at'];
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>'shown',
            'type'=>'item',
            'title'=>$title,
            'index_fields'=>$index_fields,
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

    public function liveItems()
    {
        $ids=[];
        foreach (Item::all() as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->latest()->first();
            if ($auction_item){
                if ($auction_item->more_details['status']=='live') {
                    $ids[]=$item->id;
                }
            }
        }
        $rows=$this->model->whereIn('id',$ids)->latest()->get();
        $title='قائمة السلع فى المزاد المباشر';
        $index_fields=['الرقم التسلسلى' => 'id','تاريخ الطلب'=>'created_at'];
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>'shown',
            'type'=>'item',
            'title'=>$title,
            'index_fields'=>$index_fields,
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

    public function expiredItems()
    {
        $ids=[];
        foreach (Item::all() as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->latest()->first();
            if ($auction_item){
                if ($auction_item->more_details['status']!='live' && $auction_item->more_details['status']!='soon') {
                    $ids[]=$item->id;
                }
            }
        }
        $rows=$this->model->whereIn('id',$ids)->latest()->get();
        $title='قائمة السلع بعد المزاد المباشر';
        $index_fields=['الرقم التسلسلى' => 'id','تاريخ الطلب'=>'created_at'];
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>'shown',
            'type'=>'item',
            'title'=>$title,
            'index_fields'=>$index_fields,
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
