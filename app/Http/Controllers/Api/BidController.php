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
    function lang(){
        if (\request()->header('lang')){
            return \request()->header('lang');
        }else{
            return 'ar';
        }
    }
    function liveResponse($item)
    {
        $auction_item=AuctionItem::where('item_id',$item->id)->orderBy('created_at','desc')->first();
        $is_favourite=false;
        $my_item=false;
        $win=false;
        if (\request()->user()){
            //favourite
            $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$item->id])->first();
            if ($favourite){
                $is_favourite=true;
            }
            if ($item->user_id==\request()->user()->id){
                $my_item=true;
            }
            $soon_winner=AuctionUser::where('item_id',$item->id)->latest()->value('user_id');
            if ($soon_winner){
                if ($soon_winner==\request()->user()->id){
                    $win=true;
                }
            }
            $features=$auction_item->auctionTypeFeatures(auth()->user()->id);
        }else{
            $features=$auction_item->auctionTypeFeatures();
        }
        //status
        $auction_status=$features['status'];
        $negotiation=$features['negotiation'];
        $direct_pay=$features['direct_pay'];
        $user_price=$features['user_price'];
        return [
            'id'=> (int) $item->id,
            'images'=> $item->images,
            'start_date'=> $auction_item->start_date,
            'auction_duration'=>$auction_item->auction->duration,
            'item_status'=> $item->item_status->name[$item->lang()],
            'auction_price'=> $auction_item->price,
            'name'=> $item->mark->name[$item->lang()].' '.$item->model->name[$item->lang()].' '.$item->year,
            'city'=> $item->city->name[$item->lang()],
            'mark'=> $item->mark->name[$item->lang()],
            'model'=> $item->model->name[$item->lang()],
            'year'=> $item->year??0,
            'fetes'=> $item->fetes->name[$item->lang()],
            'kms_count'=> $item->kms_count,
            'color'=> $item->color?$item->color->name[$item->lang()]:"",
            'sunder_count'=> $item->sunder_count,
            'auction_type'=> $item->auction_type->name[$item->lang()],
            'is_favourite'=> $is_favourite,
            'auction_status'=>$auction_status,
            'negotiation'=>$negotiation,
            'direct_pay'=>$direct_pay,
            'user_price'=>$user_price,
            'bid_count'=>(int)AuctionUser::where(['auction_id'=>$auction_item->auction_id,'item_id'=>$item->id])->count(),
            'my_item'=>$my_item,
            'tax'=> $item->tax==='true',
            'win'=>$win
        ];
    }

    public function liveItem():object{
        $auction_items=AuctionItem::where('more_details->status','!=','paid')->where('more_details->status','!=','expired')->where('more_details->status','!=','negotiation')->where('more_details->status','!=','delivered')->latest()->get();
        foreach ($auction_items as $soon_item){
            $end_auction=Carbon::createFromTimestamp($soon_item->start_date)->addSeconds($soon_item->auction->duration);
            $start_auction=Carbon::createFromTimestamp($soon_item->start_date);
            if (($start_auction <= Carbon::now() )  &&  ($end_auction >= Carbon::now())){
                $item=Item::find($soon_item->item_id);
                $next_items=AuctionItem::where('start_date','>',Carbon::now()->timestamp)->where('auction_id',$soon_item->auction_id)->pluck('item_id');
                $data['live']=$this->liveResponse($item);
                $data['next']=new ItemCollection(Item::whereIn('id',$next_items)->latest()->get());
                return $this->sendResponse($data);
            }
        }
        return $this->sendResponse(new Object_());
    }
    public function bid($item_id,Request $request){
        $user=$request->user();
//        $serviceAccount = ServiceAccount::fromJsonFile('/var/www/html/e-auction/mazadat-eb79528aefd3.json');
//
//        $firestore = (new Factory)
//            ->withServiceAccount($serviceAccount)
//            ->createFirestore();
//
//        $collection = $firestore->collection('liveAuctions');
//        $snapshot = $collection->documents();
//        $auctions=[];
//        foreach ($snapshot as $document) {
//            $auction['id']=$document['id'];
//            $auction['user_price']=$document['user_price'];
//            $auctions[]=$auction;
//        }
//        return $auctions;
        $auction_item=AuctionItem::where('item_id',$item_id)->latest()->first();
        if ($auction_item->more_details!=null){
            if ($auction_item->more_details['status']=='expired'  || $auction_item->more_details['status']=='paid'){
                return $this->sendError('هذا السلعة قد انتهى وقت المزايدة عليها :(');
            }
        }
        if ($user->profileAndPurchasingPowerIsFilled()==false){
            return $this->sendError(' يجب اكمال بيانات ملفك الشخصى أولا وشحن قوتك الشرائية');
        }
        if ($this->validate_purchasing_power($user,$auction_item->price+$request['charge_price'])!==true){
            return $this->validate_purchasing_power($user,$auction_item->price+$request['charge_price']);
        }
        AuctionUser::create([
            'finish_papers'=>$request->input('finish_papers',0),
            'user_id'=>$user->id,
            'item_id'=>$item_id,
            'auction_id'=>$auction_item->auction_id,
            'charge_price'=>$request['charge_price']
        ]);
        $auction_item->update([
            'price'=>$auction_item->price+$request['charge_price'],
            'latest_charge'=>$request['charge_price']
        ]);
        if (!(Carbon::createFromTimestamp($auction_item->auction->more_details['end_date']) >= Carbon::now()) && ((Carbon::createFromTimestamp($auction_item->auction->start_date)) <= Carbon::now()) ) {
            $this->charge_notify($auction_item,$user,$request['charge_price']);
        }
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
            if($user->id == $user_notify->id){
                continue;
            }
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

}
