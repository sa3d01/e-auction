<?php

namespace App\Http\Controllers;

use App\AuctionItem;
use App\AuctionUser;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
use App\Offer;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){
        auth()->setDefaultDriver('api');
    }

    public function authUser()
    {
        try {
            $user = auth()->userOrFail();
        } catch (UserNotDefinedException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        return $user;
    }

    function check_negotiation_auctions(){
        $now=Carbon::now();
        $negotiation_auction_items=AuctionItem::where('more_details->status', 'negotiation')->get();
        foreach ($negotiation_auction_items as $negotiation_auction_item){
            if ( Carbon::createFromTimestamp($negotiation_auction_item->more_details['start_negotiation'])->addSeconds(Setting::value('negotiation_period'))->timestamp < $now->timestamp ) {
                $admin_title['ar'] = 'تم انتهاء مدة المفاوضة على السلعة رقم ' . $negotiation_auction_item->item_id;
                $this->notify_admin($admin_title, $negotiation_auction_item);
                $owner_title['ar'] = 'حظ أوفر المره القادمه ! تم انتهاء مدة المفاوضة على سلعتك رقم ' . $negotiation_auction_item->item_id;
                $owner_title['en'] = 'timeout negotiation on your item ,id:' . $negotiation_auction_item->item_id;
                $this->base_notify($owner_title, $negotiation_auction_item->item->user_id, $negotiation_auction_item->item_id);
                $negotiation_auction_item->update([
                    'vip' => 'false',
                    'more_details' => [
                        'start_negotiation' => $negotiation_auction_item->more_details['start_negotiation'],
                        'end_negotiation' => $now->timestamp,
                        'true_end' => Carbon::createFromTimestamp($negotiation_auction_item->more_details['start_negotiation'])->addSeconds(Setting::value('negotiation_period'))->timestamp,
                        'status' => 'expired',
                    ]
                ]);
                $negotiation_auction_item->item->update([
                    'status' => 'expired'
                ]);
                $expired_offers = Offer::where('auction_item_id', $negotiation_auction_item->id)->get();
                foreach ($expired_offers as $expired_offer) {
                    $expired_offer->update([
                        'status' => 'expired'
                    ]);
                }
            }
        }
    }

    function auctionItemStatusUpdate()
    {
        $this->check_negotiation_auctions();
        $now = Carbon::now();
        $auction_items = AuctionItem::where('more_details->status', '!=', 'paid')->where('more_details->status', '!=', 'delivered')->where('more_details->status', '!=', 'expired')->where('more_details->status', '!=', 'negotiation')->get();
        foreach ($auction_items as $auction_item) {
            //notifies
            $admin_expired_title['ar'] = 'تم انتهاء المزاد على السلعة رقم ' . $auction_item->item_id;
            $admin_expired_title['en'] = 'auction expired on item id: ' . $auction_item->item_id;
            $admin_paid_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
            $admin_paid_title['en'] = 'item paid ,id: ' . $auction_item->item_id;
            $owner_expired_title['ar'] = 'حظ أوفر المره القادمه ! لم يتم المزايده من قبل أحد على مزادك رقم ' . $auction_item->item_id;
            $owner_expired_title['en'] = 'no body bid on your item ,id: ' . $auction_item->item_id;
            $owner_paid_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
            $owner_paid_title['en'] = 'congratulation :) ,your item is paid ,id: ' . $auction_item->item_id;
            $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
            $winner_title['en'] = 'congratulation :) ,you win in auction id: ' . $auction_item->item_id;
            if ((Carbon::createFromTimestamp($auction_item->start_date) <= $now) && (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) >= $now)) {
                $this->auction_item_update($auction_item,'live');
                $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
            } elseif (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) < $now) {
                $soon_winner = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                if ($auction_item->item->auction_type_id==4) {
                    if ($auction_item->item->price <= $auction_item->price)
                    {
                        $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id,'clickable');
                        $latest_auction_user = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                        //winner
                        $auction_item_data=$this->pay($auction_item,$latest_auction_user,$latest_auction_user->charge_price,$auction_item->price,'achieve_top_requested');
                        $auction_item->update($auction_item_data);
                        //owner
                        $this->editWallet($latest_auction_user->item->user,$auction_item->price);
                        $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->notify_admin($admin_paid_title, $auction_item);
                        $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                    }elseif ($soon_winner) {
                        $this->auction_item_update($auction_item,'negotiation');
                        $this->autoSendOffer($auction_item);
                    } else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item,'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                    }
                }
                elseif ($auction_item->item->auction_type_id==2) {
                    if ($soon_winner) {
                        $this->auction_item_update($auction_item,'negotiation');
                        $this->autoSendOffer($auction_item);
                    } else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item,'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                    }
                }
                elseif ($auction_item->item->auction_type_id==3) {
                    if ($auction_item->item->price <= $auction_item->price) {
                        $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id,'clickable');
                        $latest_auction_user = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                        //winner
                        $auction_item_data=$this->pay($auction_item,$latest_auction_user,$latest_auction_user->charge_price,$auction_item->price,'achieve_top_requested');
                        $auction_item->update($auction_item_data);
                        //owner
                        $this->editWallet($latest_auction_user->item->user,$auction_item->price);
                        $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->notify_admin($admin_paid_title, $auction_item);
                        $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                    }
                    elseif ($soon_winner) {
                        if ($auction_item->price < $auction_item->item->price) {
                            $this->auction_item_update($auction_item,'negotiation');
                            $this->autoSendOffer($auction_item);
                        } else {
                            $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id,'clickable');
                            $latest_auction_user = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                            //winner
                            $auction_item_data=$this->pay($auction_item,$latest_auction_user,$latest_auction_user->charge_price,$auction_item->price,'top_bid');
                            $auction_item->update($auction_item_data);
                            //owner
                            $this->editWallet($latest_auction_user->item->user,$auction_item->price);
                            $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                            $this->notify_admin($admin_paid_title, $auction_item);
                            $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                        }
                    }
                    else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item,'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                    }
                }
                else {
                    if ($soon_winner) {
                        $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id,'clickable');
                        $latest_auction_user = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                        //winner
                        $auction_item_data=$this->pay($auction_item,$latest_auction_user,$latest_auction_user->charge_price,$auction_item->price,'top_bid');
                        $auction_item->update($auction_item_data);
                        //owner
                        $this->editWallet($latest_auction_user->item->user,$auction_item->price);
                        $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->notify_admin($admin_paid_title, $auction_item);
                        $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                    } else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item,'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id',$auction_item->id)->get());
                    }
                }
            } else {
                $this->auction_item_update($auction_item,'soon');
            }
        }
    }

    function auction_item_update($auction_item,$status){
        if ($status=='expired'){
            $data=[
                'vip' => 'false',
                'more_details' => [
                    'status' => $status
                ]
            ];
        }elseif ($status=='negotiation'){
            $data=[
                'vip' => 'false',
                'more_details' => [
                    'status' => $status,
                    'start_negotiation'=>Carbon::now()->timestamp
                ]
            ];
        }else{
            $data=[
                'more_details' => [
                    'status' => $status
                ]
            ];
        }
        $auction_item->update($data);
    }

    public function editWallet($user, $amount)
    {
        $user->update([
            'wallet' => $user->wallet + ($amount)
        ]);
    }

    function totalAmount($auction_item)
    {
        return (integer)$auction_item->price;
    }

    function remainCalc($auction_item,$price,$take)
    {
        $app_ratio=(Setting::value('app_ratio')*($price))/100;
        $tax_ratio=Setting::value('tax_ratio');
        $owner_tax_ratio=Setting::value('owner_tax_ratio');
        $first_remain=$price-$take;
        $second_remain=$first_remain+$app_ratio+$tax_ratio;
        if ($auction_item->item->tax=='true'){
            $latest_remain=$second_remain+(($price+$app_ratio+$tax_ratio)*$owner_tax_ratio/100);
        }else{
            $latest_remain=$second_remain+(($app_ratio+$tax_ratio)*$owner_tax_ratio/100);
        }
        return $latest_remain;
    }

    public function pay($auction_item,$auction_user,$charge_price,$price,$pay_type):array
    {
        $winner=User::find($auction_user->user_id);
        $take=(10*($price))/100;
        $winner->update([
            'purchasing_power'=> ((integer)$winner->purchasing_power-$take)
        ]);
//
        $latest_remain=$this->remainCalc($auction_item,$price,$take);
        $auction_user->update([
            'more_details'=>[
                'status'=>'pending_for_transfer',
                'total_amount'=>$price,
                'paid'=>$take,
                'remain'=>$latest_remain,
            ]
        ]);
        $this->editWallet($winner,-$latest_remain);
        $data=[
            'vip' => 'false',
            'price' => $price??0,
            'latest_charge' => $charge_price??0,
            'more_details' => [
                'status'=>'paid',
                'pay_type' => $pay_type
            ]
        ];
        if ($auction_item->more_details['status']=='soon'){
            $this->reOrderAuctionItems($auction_item);
        }
        return $data;
    }

    public function reOrderAuctionItems($auction_item)
    {
        $auction=$auction_item->auction;
        $after_auction_items=AuctionItem::where('auction_id',$auction->id)->where('id','>',$auction_item->id)->get();
        foreach ($after_auction_items as $after_auction_item){
            $new_date=Carbon::createFromTimestamp($after_auction_item->start_date)->subSeconds($auction->duration)->timestamp;
            $after_auction_item->update([
                'start_date'=> $new_date
            ]);
        }
        $new_end_date=Carbon::createFromTimestamp($auction->more_details['end_date'])->subSeconds($auction->duration)->timestamp;
        $auction->update([
            'more_details'=>[
                'end_date'=>$new_end_date
            ]
        ]);
    }

    function expire_offers($expired_offers){
        foreach ($expired_offers as $expired_offer){
            $expired_offer->update([
                'status'=>'expired'
            ]);
        }
    }

    function expire_item($item){
        $item->update([
            'status'=>'expired'
        ]);
    }

    function autoSendOffer($auction_item)
    {
        $auction_user = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
        $offer = Offer::create([
            'sender_id' => $auction_user->user_id,
            'receiver_id' => $auction_item->item->user_id,
            'auction_item_id' => $auction_item->id,
            'price' => $auction_item->price,
            'status' => 'pending'
        ]);
        $title['ar'] = 'تم انتهاء المزاد على سلعتك رقم ' . $offer->auction_item->item_id . ' بسعر ' . $auction_item->price;
        $data = [];
        $data['title'] = $title;
        $data['note'] = $title;
        $data['receiver_id'] = $offer->receiver_id;
        $data['item_id'] = $offer->auction_item->item_id;
        $data['more_details'] = ['offer_id' => $offer->id];
        Notification::create($data);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $title['ar'],
                'status' => $offer->status,
                'type' => 'offer',
                'db'=>true,
                'item' => new ItemResource(Item::find($offer->auction_item->item_id)),
                'offer_id' => $offer->id
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($offer->receiver->device['id'])
            ->send();
    }

    function base_notify($title, $receiver_id, $item_id,$win=null)
    {
        $data = [];
        $data['title'] = $title;
        $data['note'] = $title;
        $data['receiver_id'] = $receiver_id;
        $data['item_id'] = $item_id;
        $data['more_details']=[
            'win'=>$win!=null
        ];
        Notification::create($data);
        try {
            $push = new PushNotification('fcm');
            $msg = [
                'notification' => array('title' => $title['ar'], 'sound' => 'default'),
                'data' => [
                    'title' => $title['ar'],
                    'body' => $title['ar'],
                    'status' => 'paid',
                    'type' => 'win',
                    'db'=>true,
                    'item' => new ItemResource(Item::find($item_id)),
                    'win'=> $win!=null
                ],
                'priority' => 'high',
            ];
            $receiver = User::find($receiver_id);
            $push->setMessage($msg)
                ->setDevicesToken($receiver->device['id'])
                ->send();
        }catch (\Exception $e){

        }

    }

    function notify_admin($title, $auction_item)
    {
        $data['title'] = $title;
        $data['item_id'] = $auction_item->item_id;
        $data['type'] = 'admin';
        $data['admin_notify_type'] = 'all';
        Notification::create($data);
    }
}
