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

    function auctionItemStatusUpdate()
    {
        $negotiation_auction_items=AuctionItem::where('more_details->status', 'negotiation')->get();
        foreach ($negotiation_auction_items as $negotiation_auction_item){
            if ( (Carbon::createFromTimestamp($negotiation_auction_item->more_details['start_negotiation'])->addSeconds(Setting::value('negotiation_period')) > Carbon::now()) ) {
                $admin_title['ar'] = 'تم انتهاء مدة المفاوضة على السلعة رقم ' . $negotiation_auction_item->item_id;
                $this->notify_admin($admin_title, $negotiation_auction_item);
                $owner_title['ar'] = 'حظ أوفر المره القادمه ! تم انتهاء مدة المفاوضة على سلعتك رقم ' . $negotiation_auction_item->item_id;
                $this->base_notify($owner_title, $negotiation_auction_item->item->user_id, $negotiation_auction_item->item_id);
                $negotiation_auction_item->update([
                    'vip' => 'false',
                    'more_details' => [
                        'start_negotiation'=>$negotiation_auction_item->more_details['start_negotiation'],
                        'end_negotiation'=>Carbon::now()->timestamp,
                        'status' => 'expired',
                    ]
                ]);
                $negotiation_auction_item->item->update([
                    'status'=>'expired'
                ]);
            }
        }

        $auction_items = AuctionItem::where('more_details->status', '!=', 'paid')->where('more_details->status', '!=', 'expired')->where('more_details->status', '!=', 'negotiation')->get();
        foreach ($auction_items as $auction_item) {
            if ((Carbon::createFromTimestamp($auction_item->start_date) <= Carbon::now()) && (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) >= Carbon::now())) {
                $auction_item->update([
                    'more_details' => [
                        'status' => 'live'
                    ]
                ]);
                $expired_offers=Offer::where('auction_item_id',$auction_item->id)->get();
                foreach ($expired_offers as $expired_offer){
                    $expired_offer->update([
                       'status'=>'expired'
                    ]);
                }
            } elseif (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) < Carbon::now()) {
                if ($auction_item->item->auction_type_id==4 || $auction_item->item->auction_type_id==2) {
                    $soon_winner = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                    if ($soon_winner) {
                        $auction_item->update([
                            'vip' => 'false',
                            'more_details' => [
                                'status' => 'negotiation',
                                'start_negotiation'=>Carbon::now()->timestamp
                            ]
                        ]);
                        $this->autoSendOffer($auction_item);
                    } else {
                        $admin_title['ar'] = 'تم انتهاء المزاد على السلعة رقم ' . $auction_item->item_id;
                        $this->notify_admin($admin_title, $auction_item);
                        $owner_title['ar'] = 'حظ أوفر المره القادمه ! لم يتم المزايده من قبل أحد على مزادك رقم ' . $auction_item->item_id;
                        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
                        $auction_item->update([
                            'vip' => 'false',
                            'more_details' => [
                                'status' => 'expired'
                            ]
                        ]);
                        $auction_item->item->update([
                            'status'=>'expired'
                        ]);
                    }
                }elseif ($auction_item->item->auction_type_id==3) {
                    $soon_winner = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                    if ($soon_winner) {
                        if ($auction_item->price < $auction_item->item->price) {
                            $auction_item->update([
                                'vip' => 'false',
                                'more_details' => [
                                    'status' => 'negotiation',
                                    'start_negotiation'=>Carbon::now()->timestamp
                                ]
                            ]);
                            $this->autoSendOffer($auction_item);
                        } else {
                            $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
                            $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
                            $admin_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
                            $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id);
                            $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
                            $this->notify_admin($admin_title, $auction_item);
                            $auction_item->update([
                                'vip' => 'false',
                                'more_details' => [
                                    'status' => 'paid'
                                ]
                            ]);
                        }
                    } else {
                        $admin_title['ar'] = 'تم انتهاء المزاد على السلعة رقم ' . $auction_item->item_id;
                        $this->notify_admin($admin_title, $auction_item);
                        $owner_title['ar'] = 'حظ أوفر المره القادمه ! لم يتم المزايده من قبل أحد على مزادك رقم ' . $auction_item->item_id;
                        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
                        $auction_item->update([
                            'vip' => 'false',
                            'more_details' => [
                                'status' => 'expired'
                            ]
                        ]);
                        $auction_item->item->update([
                            'status'=>'expired'
                        ]);
                    }
                }else {
                    $soon_winner = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                    if ($soon_winner) {
                        $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
                        $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
                        $admin_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
                        $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id);
                        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->notify_admin($admin_title, $auction_item);
                        $auction_item->update([
                            'vip' => 'false',
                            'more_details' => [
                                'status' => 'paid'
                            ]
                        ]);
                    } else {
                        $admin_title['ar'] = 'تم انتهاء المزاد على السلعة رقم ' . $auction_item->item_id;
                        $this->notify_admin($admin_title, $auction_item);
                        $owner_title['ar'] = 'حظ أوفر المره القادمه ! لم يتم المزايده من قبل أحد على مزادك رقم ' . $auction_item->item_id;
                        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
                        $auction_item->update([
                            'vip' => 'false',
                            'more_details' => [
                                'status' => 'expired'
                            ]
                        ]);
                        $auction_item->item->update([
                            'status'=>'expired'
                        ]);
                    }
                }
            } else {
                $auction_item->update([
                    'more_details' => [
                        'status' => 'soon'
                    ]
                ]);
            }
        }
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
                'item' => new ItemResource(Item::find($offer->auction_item->item_id)),
                'offer_id' => $offer->id
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($offer->receiver->device['id'])
            ->send();
    }

    function base_notify($title, $receiver_id, $item_id)
    {
        $data = [];
        $data['title'] = $title;
        $data['note'] = $title;
        $data['receiver_id'] = $receiver_id;
        $data['item_id'] = $item_id;
        Notification::create($data);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $title['ar'],
                'status' => 'paid',
                'type' => 'win',
                'item' => new ItemResource(Item::find($item_id)),
            ],
            'priority' => 'high',
        ];
        $receiver = User::find($receiver_id);
        $push->setMessage($msg)
            ->setDevicesToken($receiver->device['id'])
            ->send();
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
