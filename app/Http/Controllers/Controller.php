<?php

namespace App\Http\Controllers;

use App\AuctionItem;
use App\AuctionUser;
use App\Events\AuctionNegotiation;
use App\Events\GeneralEventsAuction;
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

    public function __construct()
    {
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

    //called by construct
    function auctionItemStatusUpdate()
    {
        event(new AuctionNegotiation());
        event(new GeneralEventsAuction());
    }

    function auction_item_update($auction_item, $status)
    {
        if ($status == 'expired') {
            $data = [
                'vip' => 'false',
                'more_details' => [
                    'status' => $status
                ]
            ];
        } elseif ($status == 'negotiation') {
            $data = [
                'vip' => 'false',
                'more_details' => [
                    'status' => $status,
                    'start_negotiation' => Carbon::now()->timestamp
                ]
            ];
        } else {
            $data = [
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

    function remainCalc($auction_item, $price, $take)
    {
        $app_ratio = (double)(Setting::value('app_ratio') * ($price)) / 100;
        $tax_ratio = (double)Setting::value('tax_ratio');
        $owner_tax_ratio = (double)Setting::value('owner_tax_ratio');
        $first_remain = $price - $take;
        $second_remain = $first_remain + $app_ratio + $tax_ratio;
        if ($auction_item->item->tax == 'true') {
            $latest_remain = $second_remain + (($price + $app_ratio + $tax_ratio) * $owner_tax_ratio / 100);
        } else {
            $latest_remain = $second_remain + (($app_ratio + $tax_ratio) * $owner_tax_ratio / 100);
        }
        return (int)$latest_remain;
    }

    //[auction-item,auction-user,latest-charge-price,latest-price-item-achieved,pay-type]
    public function pay($auction_item, $auction_user, $charge_price, $price, $pay_type): array
    {
        $winner = User::find($auction_user->user_id);
        //بنشوف العشره في الميه من قيمة السلعه ونخصمها من قوته الشرائيه
        $take = (10 * ($price)) / 100;
        $winner->update([
            'purchasing_power' => ((integer)$winner->purchasing_power - $take)
        ]);
        //اللي هيدفعه بعد خصم من قوته الشرائيه وتزويد الضرايب
        $latest_remain = $this->remainCalc($auction_item, $price, $take);
        $auction_user->update([
            'more_details' => [
                'status' => 'pending_for_transfer',
                'total_amount' => $price,
                'paid' => $take,
                'remain' => $latest_remain,
            ]
        ]);
        //بنعدل محفظته اننا نطرح منها المتبقي
        $this->editWallet($winner, -$latest_remain);
        $data = [
            'vip' => 'false',
            'price' => $price ?? 0,
            'latest_charge' => $charge_price ?? 0,
            'more_details' => [
                'status' => 'paid',
                'pay_type' => $pay_type
            ]
        ];
        if ($auction_item->more_details['status'] == 'soon') {
            $this->reOrderAuctionItems($auction_item);
        }
        return $data;
    }

    public function reOrderAuctionItems($auction_item)
    {
        $auction = $auction_item->auction;
        $after_auction_items = AuctionItem::where('auction_id', $auction->id)->where('id', '>', $auction_item->id)->get();
        foreach ($after_auction_items as $after_auction_item) {
            $new_date = Carbon::createFromTimestamp($after_auction_item->start_date)->subSeconds($auction->duration)->timestamp;
            $after_auction_item->update([
                'start_date' => $new_date
            ]);
        }
        $new_end_date = Carbon::createFromTimestamp($auction->more_details['end_date'])->subSeconds($auction->duration)->timestamp;
        $auction->update([
            'more_details' => [
                'end_date' => $new_end_date
            ]
        ]);
    }

    function expire_offers($expired_offers)
    {
        foreach ($expired_offers as $expired_offer) {
            $expired_offer->delete();
        }
    }

    function expire_item($item)
    {
        $item->update([
            'status' => 'expired'
        ]);
    }

    function autoSendOffer($auction_item)
    {
        $auction_user = AuctionUser::where(['item_id' => $auction_item->item_id, 'auction_id' => $auction_item->auction_id])->orderBy('created_at', 'desc')->first();
        //check for duplicates
        $pre_another_user_offer = Offer::where([
            'receiver_id' => $auction_item->item->user_id,
            'auction_item_id' => $auction_item->id,
            'status' => 'pending'
        ])->latest()->first();
        if (!$pre_another_user_offer) {
            $offer = Offer::create([
                'sender_id' => $auction_user->user_id,
                'receiver_id' => $auction_item->item->user_id,
                'auction_item_id' => $auction_item->id,
                'price' => $auction_item->price,
                'status' => 'pending'
            ]);
            $title['ar'] = 'تم إنتهاء المزاد رقم ' . $offer->auction_item->item_id . ' .يرجى الذهاب لصفحة المفاوضات';
            $title['en'] = 'Live auction #' . $offer->auction_item->item_id . 'is over ! Please go to the negotiation page ';
            $data = [];
            $data['title'] = $title;
            $data['note'] = $title;
            $data['receiver_id'] = $offer->receiver_id;
            $data['item_id'] = $offer->auction_item->item_id;
            $data['more_details'] = ['offer_id' => $offer->id];
            Notification::create($data);
            $push = new PushNotification('fcm');
            $msg = [
                'notification' => array(
                    'title' => $offer->auction_item->item->nameForSelect(),
                    'body' => $title[request()->input('lang', 'ar')],
                    'sound' => 'default'
                ),
                'data' => [
                    'title' => $offer->auction_item->item->nameForSelect(),
                    'body' => $title[request()->input('lang', 'ar')],
                    'status' => $offer->status,
                    'type' => 'offer',
                    'db' => true,
                    'item' => new ItemResource(Item::find($offer->auction_item->item_id)),
                    'offer_id' => $offer->id
                ],
                'priority' => 'high',
            ];
            $push->setMessage($msg)
                ->setDevicesToken($offer->receiver->device['id'])
                ->send();
        }
    }

    function base_notify($title, $receiver_id, $item_id, $win = null)
    {
        $data = [];
        $data['title'] = $title;
        $data['note'] = $title;
        $data['receiver_id'] = $receiver_id;
        $data['item_id'] = $item_id;
        $data['more_details'] = [
            'win' => $win != null
        ];
        Notification::create($data);
        try {
            $item = Item::find($item_id);
            $push = new PushNotification('fcm');
            $msg = [
                'notification' => array('title' => $item->nameForSelect(),
                    'body' => $title['ar'], 'sound' => 'default'),
                'data' => [
                    'title' => $item->nameForSelect(),
                    'body' => $title['ar'],
                    'status' => 'paid',
                    'type' => 'win',
                    'db' => true,
                    'item' => new ItemResource(Item::find($item_id)),
                    'win' => $win != null
                ],
                'priority' => 'high',
            ];
            $receiver = User::find($receiver_id);
            $push->setMessage($msg)
                ->setDevicesToken($receiver->device['id'])
                ->send();

            $push = new PushNotification('fcm');
            $msg = [
                'notification' => null,
                'data' => [
                    'title' => '',
                    'body' => '',
                    'type' => 'new_auction',
                    'db' => false,
                ],
                'priority' => 'high',
            ];
            $push->setMessage($msg)
                ->sendByTopic('new_auction')
                ->send();
        } catch (\Exception $e) {

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
