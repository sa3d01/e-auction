<?php

namespace App\Events;

use App\AuctionItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GeneralEventsAuction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $auction_items;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $auction_items = AuctionItem::where('more_details->status', '!=', 'paid')->where('more_details->status', '!=', 'delivered')->where('more_details->status', '!=', 'expired')->where('more_details->status', '!=', 'negotiation')->get();
        $this->auction_items=$auction_items;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
