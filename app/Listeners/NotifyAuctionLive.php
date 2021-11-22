<?php

namespace App\Listeners;

use App\Events\AuctionLive;
use App\Offer;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAuctionLive implements ShouldQueue
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
     * @param AuctionLive $event
     * @return void
     */
    public function handle(AuctionLive $event)
    {
        $auction_item = $event->auction_item;
        $this->auction_item_update($auction_item, 'live');
        $this->expire_offers(Offer::where('auction_item_id', $auction_item->id)->get());
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
    function expire_offers($expired_offers){
        foreach ($expired_offers as $expired_offer){
            $expired_offer->delete();
        }
    }
}
