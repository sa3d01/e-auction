<?php

namespace App\Events;

use App\AuctionItem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionLive
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction_item;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AuctionItem $auction_item)
    {
        $this->auction_item = $auction_item;
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
