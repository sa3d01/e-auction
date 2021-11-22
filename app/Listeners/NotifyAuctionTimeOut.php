<?php

namespace App\Listeners;

use App\AuctionItem;
use App\AuctionUser;
use App\Events\AuctionTimeOut;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
use App\Offer;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAuctionTimeOut implements ShouldQueue
{
    public $connection='database';
    public $queue='listeners';
    public $delay=60;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param AuctionTimeOut $event
     * @return void
     */
    public function handle(AuctionTimeOut $event)
    {
        $item_id = $event->auction_item->item_id;
        $item = Item::find($item_id);
        $auction_item = $event->auction_item;
        //notifies
        $admin_expired_title['ar'] = 'تم انتهاء المزاد على السلعة رقم ' . $item_id;
        $admin_expired_title['en'] = 'auction expired on item id: ' . $item_id;
        $admin_paid_title['ar'] = 'تم بيع السلعة رقم ' . $item_id;
        $admin_paid_title['en'] = 'item paid ,id: ' . $item_id;
        $owner_expired_title['ar'] = 'عميلنا العزيز, يؤسفنا عدم وجود مزايدات على مركبتكم رقم ' . $item_id . '  يمكنكم سحب المركبة او إعادة جدولتها لمزاد اخر';
        $owner_expired_title['en'] = 'Sorry, there are no bids on your vehicle #' . $item_id . ' You can either take it or reschedule it';
        $owner_paid_title['ar'] = 'عميلنا العزيز, لقد تم بيع مركبتكم رقم  ' . $item_id . ' بنجاح! . يمكنكم رفع طلب مستحقات عبر المحفظة ';
        $owner_paid_title['en'] = 'Congratulation ! you vehicle #' . $item_id . '   has been sold. Check the wallet for outstanding balance';
        $winner_title['ar'] = 'عميلنا العزيز, نبارك لكم الفوز بالمزاد رقم ' . $item_id . '   يرجى الذهاب للمحفظة وسداد المستحقات ';
        $winner_title['en'] = 'Congratulation ! you win the vehicle #' . $item_id . '   . Please check the wallet to pay the outstanding balance';
        //latest auction user bid
        $soon_winner = AuctionUser::where(['item_id' => $item_id, 'auction_id' => $auction_item->auction_id])->orderBy('id', 'desc')->first();
        //البيع المباشر
        if ($item->auction_type_id == 4) {
            //check if latest bid greater or equal item price
            if ($item->price <= $auction_item->price) {
                //winner
                //[auction-item,auction-user,latest-charge-price,latest-price-item-achieved,pay-type]
                $auction_item_data = $this->pay($auction_item, $soon_winner, $soon_winner->charge_price, $auction_item->price, 'achieve_top_requested');
                $auction_item->update($auction_item_data);
                $this->base_notify($winner_title, $soon_winner->user_id, $item_id, 'clickable');
                //owner
                $this->editWallet($item->user, $auction_item->price);
                $this->base_notify($owner_paid_title, $item->user_id, $item_id);
                $this->notify_admin($admin_paid_title, $auction_item);
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            } elseif ($soon_winner) {
                //start negotiation
                $this->auction_item_update($auction_item, 'negotiation');
                $this->autoSendOffer($auction_item);
            } else {
                //expired with no bids
                $this->notify_admin($admin_expired_title, $auction_item);
                $this->base_notify($owner_expired_title, $item->user_id, $item_id);
                $this->auction_item_update($auction_item, 'expired');
                $this->expire_item($auction_item->item);
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            }
        } elseif ($item->auction_type_id == 2) {
            if ($soon_winner) {
                $this->auction_item_update($auction_item, 'negotiation');
                $this->autoSendOffer($auction_item);
            } else {
                $this->notify_admin($admin_expired_title, $auction_item);
                $this->base_notify($owner_expired_title, $item->user_id, $item_id);
                $this->auction_item_update($auction_item, 'expired');
                $this->expire_item($auction_item->item);
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            }
        } elseif ($item->auction_type_id == 3) {
            if ($item->price <= $auction_item->price) {
                $this->base_notify($winner_title, $soon_winner->user_id, $item_id, 'clickable');
                //winner
                $auction_item_data = $this->pay($auction_item, $soon_winner, $soon_winner->charge_price, $auction_item->price, 'achieve_top_requested');
                $auction_item->update($auction_item_data);
                //owner
                $this->editWallet($soon_winner->item->user, $auction_item->price);
                $this->base_notify($owner_paid_title, $item->user_id, $item_id);
                $this->notify_admin($admin_paid_title, $auction_item);
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            } elseif ($soon_winner) {
                $this->auction_item_update($auction_item, 'negotiation');
                $this->autoSendOffer($auction_item);
            } else {
                $this->notify_admin($admin_expired_title, $auction_item);
                $this->base_notify($owner_expired_title, $item->user_id, $item_id);
                $this->auction_item_update($auction_item, 'expired');
                $this->expire_item($auction_item->item);
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            }
        } else {
            if ($soon_winner) {
                $this->base_notify($winner_title, $soon_winner->user_id, $item_id, 'clickable');
                //winner
                $auction_item_data = $this->pay($auction_item, $soon_winner, $soon_winner->charge_price, $auction_item->price, 'top_bid');
                $auction_item->update($auction_item_data);
                //owner
                $this->editWallet($soon_winner->item->user, $auction_item->price);
                $this->base_notify($owner_paid_title, $item->user_id, $item_id);
                $this->notify_admin($admin_paid_title, $auction_item);
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            } else {
                $this->notify_admin($admin_expired_title, $auction_item);
                $this->base_notify($owner_expired_title, $item->user_id, $item_id);
                $this->auction_item_update($auction_item, 'expired');
                $this->expire_item($item);
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            }
        }
    }

    function pay($auction_item, $auction_user, $charge_price, $price, $pay_type): array
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

    function expire_offers($expired_offers)
    {
        foreach ($expired_offers as $expired_offer) {
            $expired_offer->delete();
        }
    }

    function reOrderAuctionItems($auction_item)
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

    function expire_item($item)
    {
        $item->update([
            'status' => 'expired'
        ]);
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

    function editWallet($user, $amount)
    {
        $user->update([
            'wallet' => $user->wallet + ($amount)
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

}
