<?php

namespace App\Http\Controllers\Api;

use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
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

    public function liveItem():object{
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
    public function bid($item_id,Request $request):object{
        $user=$request->user();
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        if ($auction_item->more_details!=null){
            if ($auction_item->more_details['status']=='expired'  || $auction_item->more_details['status']=='paid'){
                return $this->sendError('هذا السلعة قد انتهى وقت المزايدة عليها :(');
            }
        }
        //todo : check purchasing_power
        if ($user->profileAndPurchasingPowerIsFilled()==false){
            return $this->sendError(' يجب اكمال بيانات ملفك الشخصى أولا وشحن قوتك الشرائية');
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
//    protected function validate_purchasing_power($item,$user){
//        $user_purchasing_power=$user->purchasing_power;
//        if ($user->package){
//            $user_purchasing_power=$user_purchasing_power+$user->package->purchasing_power_increase;
//        }
//        $purchasing_power_admin_percent=$this->purchasing_power_ratio;
//        return true;
//    }
}
