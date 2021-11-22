<?php

namespace App\Listeners;

use App\Events\AuctionLive;
use App\Events\AuctionTimeOut;
use App\Events\GeneralEventsAuction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class TasksGeneralEventsAuction implements ShouldQueue
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
     * @param GeneralEventsAuction $event
     * @return void
     */
    public function handle(GeneralEventsAuction $event)
    {
        $now = Carbon::now();
        $auction_items = $event->auction_items;
        foreach ($auction_items as $auction_item) {
            //live event to update status to live and expired pre-live offers
            if ($now->gt(Carbon::createFromTimestamp($auction_item->start_date)) && Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration)->gt($now)) {
                event(new AuctionLive($auction_item));
            } //after live duration checks
            elseif ($now->gt(Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration))) {
                event(new AuctionTimeOut($auction_item));
            } else {
                $this->auction_item_update($auction_item, 'soon');
            }
        }
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

}
