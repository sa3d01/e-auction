<?php

namespace App\Http\Controllers\Admin;

use App\AuctionItem;
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

    function size($size, $precision = 2):int
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }

    public function store(Request $request)
    {
        $data=$request->all();
        if($request['model_id']==null){
            return redirect()->back()->withErrors(['تأكد من اختيار موديل المركبة..']);
        }
        $items_images=[];
        if ($request->images){
            foreach ($request->images as $image){
                if (is_file($image)) {
                    if ($image->getSize() > 4194304){
                        return redirect()->back()->withErrors(['حجم الصورة كبير جدا..']);
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
        $user = User::where('email','admin@admin.com')->first();
        if (!$user){
            $package=Package::where('price','!=',0)->latest()->first();
            $user=User::create([
                'name'=>'admin',
                'phone'=>'+966538074804',
                'email'=>'admin@admin.com',
                'email_verified_at'=>Carbon::now(),
                'package_id'=>$package->id,
                'purchasing_power'=>100000,
                'status'=>1,
            ]);
        }
        $data['user_id']=$user->id;
        $data['status']='delivered';
        $data['shipping_by']='user';
        $data['pay_status']=1;
        $item=$this->model->create($data);
        return redirect()->route('admin.item.status',['status'=>'accepted'])->with('created', 'تمت الاضافة بنجاح');
    }

    public function items($status)
    {
        $rows=$this->model->where('status',$status)->latest()->get();
        if ($status=='accepted'){
            $rows=$this->model->whereIn('status',['accepted','delivered'])->latest()->get();
            $title='قائمة المركبات المطلوب اعدادها';
            $index_fields=['الرقم التسلسلى' => 'id'];
        }else{
            $title='قائمة المركبات';
            $index_fields=['الرقم التسلسلى' => 'id','تاريخ الطلب'=>'created_at'];
        }
        $fields=[
            'الرقم التسلسلى' => 'id',
//            'تاريخ الطلب'=>'created_at',
//            'صور المركباتة'=>'images',
            'عدد السندرات'=>'sunder_count',
//            'الممشى'=>'kms_count',
//            'صورة الاستمارة'=>'paper_image',
            'السعر'=>'price',
        ];
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>$status,
            'type'=>'item',
            'title'=>$title,
            'index_fields'=>$fields,
            'selects'=>[
                [
                    'name'=>'user',
                    'title'=>'المستخدم'
                ],
                [
                    'name'=>'auction_type',
                    'title'=>'نوع المزايدة'
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
        ]);
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
            'صور المركباتة'=>'images',
            'عدد السندرات'=>'sunder_count',
            'الممشى'=>'kms_count',
            'صورة الاستمارة'=>'paper_image',
            'السعر'=>'price',
        ];
        return View('dashboard.item.show', [
            'row' => $row,
            'type'=>'item',
            'action'=>'admin.item.update',
            'title'=>'بيانات المركباتة',
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

    public function vip_auction_items(){
        $row_ids=AuctionItem::where('vip','true')->pluck('item_id');
        $rows=Item::whereIn('id',$row_ids)->latest()->get();
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>'shown',
            'type'=>'item',
            'title'=>'قائمة المركبات المميزة',
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

    public function report($item_id){
        $report=Report::where('item_id',$item_id)->latest()->first();
        if (!$report){
            return View('dashboard.report.create', [
                'item_id'=>$item_id,
                'action'=>'admin.report.store',
                'title'=>'أضافة تقرير فحص',
                'create_fields'=>['العنوان' => 'title','الوصف' => 'note'],
                'languages'=>true,
                'images'=>true,
            ]);
        }
        return View('dashboard.report.show', [
            'row' => $report,
            'action'=>'admin.report.update',
            'title'=>' تقرير فحص',
            'edit_fields'=>['العنوان' => 'title','الوصف' => 'note'],
            'languages'=>true,
            'images'=>true,
        ]);
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
//        $current_images=json_decode($item->imagesArray());
        $images=[];
        if ($request->images){
            foreach ($request->images as $image){
                if (is_file($image)) {
                    if ($image->getSize() > 5142575){
                        return redirect()->back()->withErrors(['حجم الصورة كبير جدا..']);
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
//        foreach ($current_images as $old_image){
//            $images[]=$old_image;
//        }
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
                'db'=>false,
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->sendByTopic('new_auction')
            ->send();
        return redirect()->back()->with('updated');
    }

    public function item_delivered_to_garage($item_id){
        $item=Item::find($item_id);
        $item->update([
            'status'=>'delivered'
        ]);
        $note['ar']='تم تأكيد استلام مركبتك الى ساحة الحفظ من قبل الادارة ..';
        $note['en']='your added item is delivered to admin garage..';
        $this->itemStatusNotify($item,$note);
        $item->refresh();
        return redirect()->back()->with('updated');
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
//        $add_item_tax=Setting::first()->value('add_item_tax');
//        if ($add_item_tax < $item->user->wallet){
//            $this->itemTaxPay($item);
//        }
//        if ($item->pay_status==1){
//            $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..';
//            $note['en']='your added item is accepted from admin  ..';
//        }else{
//            $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..ويرجى شحن محفظتك قريبا لتحصيل ضريبة الاضافة لمزاد';
//            $note['en']='your added item is accepted from admin..please charge your wallet to add to auction';
//        }
        $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..';
        $note['en']='your added item is accepted from admin  ..';
        $this->editWallet($item->user,-$request['shipping_price']);
        $more_details['shipping_price_status']='paid';

//        if ($item->shipping_by=='app'){
//            $more_details['shipping_price']=$request['shipping_price'];
//            if ($request['shipping_price'] < $item->user->wallet){
//                $this->walletPay($item->user,$request['shipping_price'],'shipping');
//                $more_details['shipping_price_status']='paid';
//            }else{
//                $more_details['shipping_price_status']='pending';
//                $note['ar']='تم قبول اضافة منتجك من قبل الادارة ..ويرجى شحن محفظتك قريبا لتحصيل مستحقات التطبيق المالية';
//                $note['en']='your added item is accepted from admin..please charge your wallet ..';
//            }
//        }
        $item->update([
            'status'=>'accepted',
            'more_details'=>$more_details,
        ]);

        $this->itemStatusNotify($item,$note);
        $item->refresh();
        return redirect()->back()->with('updated');
    }
    public function hide($id,Request $request){
        $item=$this->model->find($id);
        if($item->status == 'shown'){
            $item->update(
                [
                    'status'=>'hidden',
                ]
            );
        }else{
            $item->update(
                [
                    'status'=>'shown',
                ]
            );
        }
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
                'db'=>true,
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
        $title='قائمة المركبات فى المزاد قبل المباشر';
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
        $title='قائمة المركبات فى المزاد المباشر';
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
        $title='قائمة المركبات بعد المزاد المباشر';
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

    public function sold_auction_items()
    {
        $solid_items = AuctionItem::where('more_details->status', 'paid')->orWhere('more_details->status', 'delivered')->pluck('item_id')->toArray();
        $rows=$this->model->whereIn('id',$solid_items)->latest()->get();
        $title='قائمة المركبات المباعة';
        $index_fields=['الرقم التسلسلى' => 'id'];
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>'sold',
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
    public function hidden_auction_items()
    {
        $solid_items = AuctionItem::where('more_details->status', 'paid')->orWhere('more_details->status', 'delivered')->pluck('item_id')->toArray();
        $rows=$this->model->whereIn('id',$solid_items)->where('status','hidden')->latest()->get();
        $title='قائمة المركبات المخفاه من التطبيق';
        $index_fields=['الرقم التسلسلى' => 'id'];
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status'=>'hidden',
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
