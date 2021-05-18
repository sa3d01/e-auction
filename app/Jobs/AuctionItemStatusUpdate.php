<?php

namespace App\Jobs;

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
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AuctionItemStatusUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $auction_items;
    public function __construct($auction_items)
    {
        $this->auction_items=$auction_items;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        throw new \Exception("Error Processing the job", 1);

        $now = Carbon::now();
//        $auction_items = AuctionItem::where('more_details->status', '!=', 'paid')->where('more_details->status', '!=', 'delivered')->where('more_details->status', '!=', 'expired')->where('more_details->status', '!=', 'negotiation')->get();
        foreach ($this->auction_items as $auction_item) {
            //notifies
            $admin_expired_title['ar'] = 'تم انتهاء المزاد على السلعة رقم ' . $auction_item->item_id;
            $admin_paid_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
            $owner_expired_title['ar'] = 'حظ أوفر المره القادمه ! لم يتم المزايده من قبل أحد على مزادك رقم ' . $auction_item->item_id;
            $owner_paid_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
            $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
            if ((Carbon::createFromTimestamp($auction_item->start_date) <= $now) && (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) >= $now)) {
                $this->auction_item_update($auction_item, 'live');
                $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
            } elseif (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) < $now) {
                $soon_winner = AuctionUser::where('item_id', $auction_item->item_id)->latest()->first();
                if ($auction_item->item->auction_type_id == 4) {
                    if ($auction_item->item->price <= $auction_item->price) {
                        $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id, 'clickable');
                        $this->addToCredit($soon_winner);
                        $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->notify_admin($admin_paid_title, $auction_item);
                        $this->auction_item_update($auction_item, 'paid');
                        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                    } elseif ($soon_winner) {
                        $this->auction_item_update($auction_item, 'negotiation');
                        $this->autoSendOffer($auction_item);
                    } else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item, 'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                    }
                } elseif ($auction_item->item->auction_type_id == 2) {
                    if ($soon_winner) {
                        $this->auction_item_update($auction_item, 'negotiation');
                        $this->autoSendOffer($auction_item);
                    } else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item, 'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                    }
                } elseif ($auction_item->item->auction_type_id == 3) {
                    if ($auction_item->item->price <= $auction_item->price) {
                        $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id, 'clickable');
                        $this->addToCredit($soon_winner);
                        $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->notify_admin($admin_paid_title, $auction_item);
                        $this->auction_item_update($auction_item, 'paid');
                        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                    } elseif ($soon_winner) {
                        if ($auction_item->price < $auction_item->item->price) {
                            $this->auction_item_update($auction_item, 'negotiation');
                            $this->autoSendOffer($auction_item);
                        } else {
                            $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id, 'clickable');
                            $this->addToCredit($soon_winner);
                            $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                            $this->notify_admin($admin_paid_title, $auction_item);
                            $this->auction_item_update($auction_item, 'paid');
                            $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                        }
                    } else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item, 'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                    }
                } else {
                    if ($soon_winner) {
                        $this->base_notify($winner_title, $soon_winner->user_id, $auction_item->item_id, 'clickable');
                        $this->addToCredit($soon_winner);
                        $this->base_notify($owner_paid_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->notify_admin($admin_paid_title, $auction_item);
                        $this->auction_item_update($auction_item, 'paid');
                        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                    } else {
                        $this->notify_admin($admin_expired_title, $auction_item);
                        $this->base_notify($owner_expired_title, $auction_item->item->user_id, $auction_item->item_id);
                        $this->auction_item_update($auction_item, 'expired');
                        $this->expire_item($auction_item->item);
                        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
                    }
                }
            } else {
                $this->auction_item_update($auction_item, 'soon');
            }
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
            $push = new PushNotification('fcm');
            $msg = [
                'notification' => array('title' => $title['ar'], 'sound' => 'default'),
                'data' => [
                    'title' => $title['ar'],
                    'body' => $title['ar'],
                    'status' => 'paid',
                    'type' => 'win',
                    'item' => new ItemResource(Item::find($item_id)),
                    'win' => $win != null
                ],
                'priority' => 'high',
            ];
            $receiver = User::find($receiver_id);
            $push->setMessage($msg)
                ->setDevicesToken($receiver->device['id'])
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

    function auction_item_update($auction_item, $status)
    {
        if ($status == 'expired' || $status == 'paid') {
            if ($status == 'paid') {
                $winner_id = AuctionUser::where(['auction_id' => $auction_item->auction_id, 'item_id' => $auction_item->item_id])->latest()->value('user_id');
                $winner = User::find($winner_id);
                $where_store_amount = AuctionUser::where(['auction_id' => $auction_item->auction_id, 'item_id' => $auction_item->item_id])->latest()->first();
                if ($winner->purchasing_power > $this->totalAmount($auction_item)) {
                    $where_store_amount->update([
                        'more_details' => [
                            'status' => 'paid',
                            'total_amount' => $this->totalAmount($auction_item),
                            'paid' => $this->totalAmount($auction_item),
                            'remain' => 0
                        ]
                    ]);
                    $winner->update([
                        'purchasing_power' => $winner->purchasing_power - $this->totalAmount($auction_item),
                    ]);
                    $data = [
                        'vip' => 'false',
                        'more_details' => [
                            'status' => 'delivered'
                        ]
                    ];
                    $note['ar'] = 'تم خصم سعر السلعة من قوتك الشرائية :)';
                    $note['en'] = 'تم خصم سعر السلعة من قوتك الشرائية :)';
                    $this->base_notify($note, $winner->id, $auction_item->item_id, true);
                } else {
                    $where_store_amount->update([
                        'more_details' => [
                            'status' => 'pending_for_transfer',
                            'total_amount' => $this->totalAmount($auction_item),
                            'remain' => $this->totalAmount($auction_item) - $winner->purchasing_power,
                            'paid' => $winner->purchasing_power
                        ]
                    ]);
                    $winner->update([
                        'purchasing_power' => 0,
//                        'credit'=>$winner->credit+($this->totalAmount($auction_item)-$winner->purchasing_power)
                    ]);
                    $data = [
                        'vip' => 'false',
                        'more_details' => [
                            'status' => 'paid'
                        ]
                    ];
                }
            } else {
                $data = [
                    'vip' => 'false',
                    'more_details' => [
                        'status' => $status
                    ]
                ];
            }
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

    function expire_item($item)
    {
        $item->update([
            'status' => 'expired'
        ]);
    }

    function expire_offers($expired_offers)
    {
        foreach ($expired_offers as $expired_offer) {
            $expired_offer->update([
                'status' => 'expired'
            ]);
        }
    }

    function addToCredit($auction_user)
    {
        $auction_item = AuctionItem::where(['item_id' => $auction_user->item_id, 'auction_id' => $auction_user->auction_id])->latest()->first();
        $latest_credit = $auction_user->item->user->credit;
        $auction_user->item->user->update([
            'credit' => (integer)($latest_credit + $auction_item->auction_price)
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
                'item' => new ItemResource(Item::find($offer->auction_item->item_id)),
                'offer_id' => $offer->id
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($offer->receiver->device['id'])
            ->send();
    }

    function totalAmount($auction_item)
    {
        $setting = Setting::first();
        $auction_price = $auction_item->auction_price;
        $auction_user = AuctionUser::where(['auction_id' => $auction_item->auction_id, 'item_id' => $auction_item->item_id])->latest()->first();
        $winner_finish_paper = $auction_user->finish_papers == 1 ? $setting->finish_papers : 0;
        $owner_tax = $auction_item->item->tax == 'true' ? ($auction_price * $setting->owner_tax_ratio / 100) : 0;
        return (integer)$auction_price + $owner_tax + ($setting->tax_ratio) + ($auction_price * $setting->app_ratio / 100) + $winner_finish_paper;
    }

}
