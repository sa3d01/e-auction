<?php

namespace App\Listeners;

use App\AuctionItem;
use App\Events\AuctionNegotiation;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
use App\Offer;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;

class NotifyAuctionNegotiation
{
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
     * @param AuctionNegotiation $event
     * @return void
     */
    public function handle(AuctionNegotiation $event)
    {
        $now = Carbon::now();
        $negotiation_auction_items = AuctionItem::where('more_details->status', 'negotiation')->get();
        foreach ($negotiation_auction_items as $negotiation_auction_item) {
            $end_negotiation = Carbon::createFromTimestamp($negotiation_auction_item->more_details['start_negotiation'])->addSeconds(Setting::value('negotiation_period'));
            if ($now->gt($end_negotiation)) {
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
                    $expired_offer->delete();
                }
            }
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
}
