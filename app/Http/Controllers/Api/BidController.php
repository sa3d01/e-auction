<?php

namespace App\Http\Controllers\Api;

use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
use App\Offer;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Object_;

class BidController extends MasterController
{
    protected $model;

    public function __construct(Item $model)
    {
        $this->model = $model;
        $this->purchasing_power_ratio=Setting::first()->value('purchasing_power_ratio');
        parent::__construct();
    }

    public function liveItem(){
        $auction_items=AuctionItem::where('more_details->status','live')->latest()->get();
        foreach ($auction_items as $soon_item){
            $end_auction=Carbon::createFromTimestamp($soon_item->start_date)->addSeconds($soon_item->auction->duration);
            $start_auction=Carbon::createFromTimestamp($soon_item->start_date);
            if (($start_auction <= Carbon::now() )  &&  ($end_auction >= Carbon::now())){
                $item=Item::find($soon_item->item_id);
                $next_items=AuctionItem::where('start_date','>',Carbon::now()->timestamp)->where('auction_id',$soon_item->auction_id)->pluck('item_id');
                $data['live']=new ItemResource($item);
                $data['next']=new ItemCollection(Item::whereIn('id',$next_items)->latest()->get());
                return $this->sendResponse($data);
            }
        }
        return $this->sendResponse(new Object_());
    }

    public function bid($item_id,Request $request){
        $user=$request->user();
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        //todo : check purchasing_power
        if ($auction_item->more_details!=null){
            if ($auction_item->more_details['status']=='expired'  || $auction_item->more_details['status']=='paid'){
                return $this->sendError('هذا السلعة قد انتهى وقت المزايدة عليها :(');
            }
        }
        AuctionUser::create([
           'user_id'=>$user->id,
           'item_id'=>$item_id,
           'auction_id'=>$auction_item->auction_id,
           'charge_price'=>$request['charge_price']
        ]);
        $auction_item->update([
            'price'=>$auction_item->price+$request['charge_price'],
            'latest_charge'=>$request['charge_price']
        ]);
        $this->charge_notify($auction_item,$user,$request['charge_price']);
        //todo : add key of soon winner
        //todo : increase duration of auction
        return $this->sendResponse('تمت المزايدة بنجاح');
    }
    public function directPay($item_id,Request $request){
        $user=$request->user();
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        if ($auction_item->more_details['status']=='expired'  || $auction_item->more_details['status']=='paid'){
            return $this->sendError('هذا السلعة قد انتهى وقت المزايدة عليها :(');
        }
        $latest_auction_user=AuctionUser::where('item_id',$item_id)->latest()->first();
        if ($latest_auction_user){
            $charge_price=$auction_item->item->price-$auction_item->price;
        }else{
            $charge_price=$auction_item->item->price;
        }
        AuctionUser::create([
            'user_id'=>$user->id,
            'item_id'=>$item_id,
            'auction_id'=>$auction_item->auction_id,
            'charge_price'=>$charge_price
        ]);
        $auction_item->update([
            'price'=>$auction_item->item->price,
            'latest_charge'=>$charge_price,
            'more_details'=>[
                'status'=>'paid',
                'pay_type'=>'direct_pay'
            ]
        ]);
        $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
        $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
        $admin_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
        $this->base_notify($winner_title, $user->id, $auction_item->item_id);
        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
        $this->notify_admin($admin_title, $auction_item);
        return $this->sendResponse('تمت العملية بنجاح');
    }
    public function sendOffer($item_id,Request $request){
        $item=Item::find($item_id);
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        $sender=$request->user();
        if ($sender->id == $item->user_id){
            $latest_offer=Offer::where('auction_item_id',$auction_item->id)->latest()->first();
            if ($latest_offer->sender_id==$sender->id){
                $receiver=User::find($latest_offer->receiver_id);
            }else{
                $receiver=User::find($latest_offer->sender_id);
            }
        }else{
            $receiver=User::find($item->user_id);
        }
        $pending_offer=Offer::where(['sender_id'=>$sender->id,'receiver_id'=>$receiver->id,'auction_item_id'=>$auction_item->id,'status'=>'pending'])->latest()->first();
        if ($pending_offer){
            return $this->sendError('لم يتم الرد على عرضك الأخير');
        }
        if ($item->price != null){
            if ($item->price < $request['price']){
                return $this->sendError('عرض السعر المقدم أعلى من السعر المحدد من المالك');
            }
        }
        $offers=Offer::where(['auction_item_id'=>$auction_item->id,'receiver_id'=>$receiver->id])->orWhere(['auction_item_id'=>$auction_item->id,'sender_id'=>$receiver->id])->latest()->get();
        foreach ($offers as $old_offer){
            $old_offer->update([
                'status'=>'opposite'
            ]);
        }
        $offer=Offer::create([
            'sender_id'=>$sender->id,
            'receiver_id'=>$receiver->id,
            'auction_item_id'=>$auction_item->id,
            'price'=>$request['price'],
            'status'=>'pending'
        ]);
        $this->notify($offer);
        return $this->sendResponse('تم الإرسال بنجاح');
    }
    public function acceptOffer($item_id,$offer_id,Request $request):string{
        $user=$request->user();
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        if ($auction_item->more_details['status']=='expired'  || $auction_item->more_details['status']=='paid'){
            return $this->sendError('هذا السلعة قد انتهى وقت المزايدة عليها :(');
        }
        $offer=Offer::find($offer_id);
        $charge_price=$offer->price;
        AuctionUser::create([
            'user_id'=>$offer->sender_id,
            'item_id'=>$item_id,
            'auction_id'=>$auction_item->auction_id,
            'charge_price'=>$charge_price
        ]);
        $auction_item->update([
            'price'=>$offer->price,
            'latest_charge'=>$charge_price,
            'more_details'=>[
                'status'=>'paid',
                'pay_type'=>'negotiation'
            ]
        ]);
        $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
        $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
        $admin_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
        $this->base_notify($winner_title, $offer->sender_id, $auction_item->item_id);
        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
        $this->notify_admin($admin_title, $auction_item);
        $offers=Offer::where('auction_item_id',$auction_item->id)->get();
        foreach ($offers as $offer){
            $offer->delete();
        }
        return $this->sendResponse('تمت العملية بنجاح');
    }
    public function refuseOffer($item_id,Request $request){
        $user=$request->user();
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        $item=Item::find($item_id);
        if (($user->id==$item->user_id) && ($auction_item->status=='negotiation')){
            $item->update([
                'status'=>'accepted',
                'reason'=>'resale'
            ]);
            $auction_item->delete();
            $notifications=Notification::where('item_id',$item_id)->get();
            foreach ($notifications as $notification){
                $notification->delete();
            }
        }
        $latest_offer=Offer::where('auction_item_id',$auction_item->id)->latest()->first();
        $latest_offer->update([
            'status'=>'rejected'
        ]);
        $opposite_offer=Offer::where('auction_item_id',$auction_item->id)->where('status','opposite')->latest()->first();
        if ($opposite_offer){
            $opposite_offer->update([
               'status'=>'pending'
            ]);
        }
        if ($latest_offer->sender_id==$user->id){
            $receiver=User::find($latest_offer->receiver_id);
        }else{
            $receiver=User::find($latest_offer->sender_id);
        }
        $title['ar'] = 'لقد تم رفض عرض السعر المقدم من قبلك على المزاد رقم '. $auction_item->item_id;
        $data=[];
        $data['title']=$title;
        $data['note']=$title;
        $data['receiver_id']=$receiver->id;
        $data['item_id']=$auction_item->item_id;
        $data['more_details']=['offer_id'=>$latest_offer->id];
        Notification::create($data);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title'=>$title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $title['ar'],
                'status' => 'refuse_offer',
                'type'=>'refuse_offer',
                'offer_id'=>$latest_offer->id
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($receiver->device['id'])
            ->send();
        return $this->sendResponse('تمت العملية بنجاح');
    }
    public function itemOffers($item_id):object{
        $user=\request()->user();
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        if (!$auction_item){
            return $this->sendError('توجد مشكله ما');
        }
        $offers=Offer::where(['auction_item_id'=>$auction_item->id])->where('status','pending')->latest()->get();
        $data=[];
        foreach ($offers as $offer){
            $arr['id']=$offer->id;
            $arr['price']=$offer->price;
            $arr['user_id']=$offer->sender_id;
            $arr['item']=new ItemResource(Item::find($item_id));
            $data[]=$arr;
        }
        return $this->sendResponse($data);
    }
    public function notify($offer){
        $title['ar'] = 'تم إرسال عرض اليك على المزاد رقم '. $offer->auction_item->item_id;
        $title['en'] = 'تم إرسال عرض اليك على المزاد رقم '. $offer->auction_item->item_id;
        $data=[];
        $data['title']=$title;
        $data['note']=$title;
        $data['receiver_id']=$offer->receiver_id;
        $data['item_id']=$offer->auction_item->item_id;
        $data['more_details']=['offer_id'=>$offer->id];
        Notification::create($data);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title'=>$title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $title['ar'],
                'status' => $offer->status,
                'type'=>'offer',
                'item'=>new ItemResource(Item::find($offer->auction_item->item_id)),
                'offer_id'=>$offer->id
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($offer->receiver->device['id'])
            ->send();
    }
    public function charge_notify($auction_item,$user,$charge_price){
        $users_id=AuctionUser::where(['item_id'=>$auction_item->item_id,'auction_id'=>$auction_item->auction_id])->groupBy('user_id')->pluck('user_id')->toArray();
        $fav_users_id=Favourite::where(['item_id'=>$auction_item->item_id,'auction_id'=>$auction_item->auction_id])->groupBy('user_id')->pluck('user_id')->toArray();
        $users = User::whereIn('id',array_merge($users_id,$fav_users_id,(array)$auction_item->item->user_id))->get();
        $title['ar'] = 'تم إضافة مزايدة جديدة بقيمة '. $charge_price . 'ريال سعودى عن طريق مستخدم رقم ' .$user->id .' بمزاد رقم '.$auction_item->item->id;
        $title['en'] = 'تم إضافة مزايدة جديدة بقيمة '. $charge_price . 'ريال سعودى عن طريق مستخدم رقم ' .$user->id .'بمزاد رقم '.$auction_item->item->id;
        foreach ($users as $user_notify) {
            if($user == $user_notify)
                continue;
            $data=[];
            $data['title']=$title;
            $data['note']=$title;
            $data['receiver_id']=$user_notify->id;
            $data['item_id']=$auction_item->item_id;
            Notification::create($data);
            $push = new PushNotification('fcm');
            $msg = [
                'notification' => array('title'=>$title['ar'], 'sound' => 'default'),
                'data' => [
                    'title' => $title['ar'],
                    'body' => $title['ar'],
                    'status' => 'auction',
                    'type'=>'auction',
                    'item'=>new ItemResource(Item::find($auction_item->item_id)),
                    'price'=>$auction_item->price
                ],
                'priority' => 'high',
            ];
            $push->setMessage($msg)
                ->setDevicesToken($user_notify->device['id'])
                ->send();
        }
        $this->notify_admin($title,$auction_item);
    }
    public function notify_admin($title,$auction_item){
        $data['title']=$title;
        $data['item_id']=$auction_item->item_id;
        $data['type']='admin';
        $data['admin_notify_type']='all';
        Notification::create($data);
    }
    protected function validate_purchasing_power($item,$user){
        $user_purchasing_power=$user->purchasing_power;
        if ($user->package){
            $user_purchasing_power=$user_purchasing_power+$user->package->purchasing_power_increase;
        }
        $purchasing_power_admin_percent=$this->purchasing_power_ratio;
        return true;
    }
}
