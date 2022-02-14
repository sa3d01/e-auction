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
use Illuminate\Support\Facades\Session;
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
        $can_bid=true;
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
        $now=Carbon::now();
        $bid_pause_period=Setting::value('bid_pause_period');
        if ($auction_item->more_details['status'] == 'soon' && ($now->diffInSeconds(Carbon::createFromTimestamp($auction_item->auction->start_date))) < $bid_pause_period){
            $can_bid=false;
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
            'start_date_text'=> Carbon::createFromTimestamp($auction_item->start_date),
            'auction_duration'=>$auction_item->auction->duration,
            'item_status'=> $item->item_status->name[$this->lang()],
            'auction_price'=> $auction_item->price,
            'name'=> $item->year.' '.$item->mark->name[$this->lang()].' '.$item->model->name[$this->lang()],
            'city'=> $item->city->name[$this->lang()],
            'mark'=> $item->mark->name[$this->lang()],
            'model'=> $item->model->name[$this->lang()],
            'year'=> $item->year??0,
            'fetes'=> $item->fetes->name[$this->lang()],
            'kms_count'=> $item->kms_count,
            'color'=> $item->color?$item->color->name[$this->lang()]:"",
            'sunder_count'=> $item->sunder_count,
            'auction_type'=> $item->auction_type->name[$this->lang()],
            'is_favourite'=> $is_favourite,
            'auction_status'=>$auction_status,
            'negotiation'=>$negotiation,
            'direct_pay'=>$direct_pay,
            'user_price'=>$user_price,
            'bid_count'=>(int)AuctionUser::where(['auction_id'=>$auction_item->auction_id,'item_id'=>$item->id])->count(),
            'my_item'=>$my_item,
            'tax'=> $item->tax==='true',
            'win'=>$win,
            'can_bid'=>$can_bid
        ];
    }
    public function liveItem():object{
        $auction_items=AuctionItem::where('more_details->status','!=','paid')->where('more_details->status','!=','expired')->where('more_details->status','!=','negotiation')->where('more_details->status','!=','delivered')->latest()->get();
        foreach ($auction_items as $soon_item){
            $end_auction=Carbon::createFromTimestamp($soon_item->start_date)->addSeconds($soon_item->auction->duration);
            $start_auction=Carbon::createFromTimestamp($soon_item->start_date);
            $now=Carbon::now();
            if ($now->between($start_auction, $end_auction)){
                $item=Item::find($soon_item->item_id);
                $next_items=AuctionItem::where('start_date','>',$now->timestamp)->where('more_details->status','soon')->where('auction_id',$soon_item->auction_id)->pluck('item_id');
                $data['live']=$this->liveResponse($item);
                $data['next']=new ItemCollection(Item::whereIn('id',$next_items)->get());
                return $this->sendResponse($data);
            }
        }
        return $this->sendResponse(new Object_());
    }
    public function bid($item_id,Request $request){
        //total_price,bid_time
        $total_price=$request['total_price'];
        return $this->sendError(gettype(Session::get('bid_total_price')));
        Session::put('bid_total_price', $total_price);
        if($total_price==0 || $total_price==''){
            return $this->sendError($this->lang()=='ar'?'لا يمكن المزايدة بتلك القيمة!':'You can\'t bid by 0 amount !');
        }
        $bid_time=$request['bid_time'];
        $user=$request->user();
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        //checkCanBid
        if ($this->canBid($user,$auction_item,$total_price,$bid_time) !== true){
            return $this->canBid($user,$auction_item,$total_price,$bid_time);
        }
        //store bid
        $this->completedBidOperations($auction_item,$user,$request->input('finish_papers', 0),$total_price,$bid_time);
        return $this->sendError($this->lang()=='ar'?'تمت المزايدة بنجاح!':'Your bid has been accepted !');
    }
    function completedBidOperations($auction_item,$user,$finish_papers,$total_price,$bid_time)
    {
        sleep(1);
        if ($total_price <= $auction_item->price){
            return $this->sendError('لا يمكن المزايدة بأقل من القيمة الحالية للمزاد');
        }
        $charge_price=$total_price-($auction_item->price);
        AuctionUser::create([
            'finish_papers' => $finish_papers,
            'user_id' => $user->id,
            'item_id' => $auction_item->item_id,
            'auction_id' => $auction_item->auction_id,
            'charge_price' => $charge_price
        ]);
        sleep(1);
        $auction_item->update([
            'price' => $auction_item->price + $charge_price,
            'latest_charge' => $charge_price
        ]);
        $this->topicNotify();
        //change time now to request bid time
        $now=Carbon::createFromTimestamp($bid_time);
//        if (!(Carbon::createFromTimestamp($auction_item->auction->more_details['end_date']) >= $now) && ((Carbon::createFromTimestamp($auction_item->auction->start_date)) <= $now)) {
//            $this->charge_notify($auction_item, $user, $charge_price);
//        }
        if ($auction_item->more_details['status']!="live") {
            $this->charge_notify($auction_item, $user, $charge_price);
        }
    }
    public function charge_notify($auction_item,$user,$charge_price){
        $users_id=AuctionUser::where(['item_id'=>$auction_item->item_id,'auction_id'=>$auction_item->auction_id])->groupBy('user_id')->pluck('user_id')->toArray();
        $fav_users_id=Favourite::where(['item_id'=>$auction_item->item_id,'auction_id'=>$auction_item->auction_id])->groupBy('user_id')->pluck('user_id')->toArray();
        $users = User::whereIn('id',array_merge($users_id,$fav_users_id,(array)$auction_item->item->user_id))->get();
        $title['ar'] = 'تم إضافة مزايدة جديدة بقيمة '. $charge_price . 'ريال سعودى عن طريق مستخدم رقم ' .$user->id .' بمزاد رقم '.$auction_item->item->id;
        $title['en'] = 'there is new bid with amount '. $charge_price . 'SR by user number ' .$user->id .'auction ID '.$auction_item->item->id;
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
                'notification' => array('title' => $auction_item->item->nameForSelect(),'body' => $title['ar'], 'sound' => 'default'),
                'data' => [
                    'title' => $auction_item->item->nameForSelect(),
                    'body' => $title['ar'],
                    'status' => 'auction',
                    'type'=>'auction',
                    'db'=>true,
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
    function topicNotify()
    {
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => null,
            'data' => [
                'title' => '',
                'body' => '',
                'type' => 'new_auction',
                'db'=>false,
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->sendByTopic('new_auction')
            ->send();
    }
}
