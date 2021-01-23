<?php

namespace App\Observers;

use App\AuctionItem;
use App\AuctionUser;
use App\Item;
use App\Notification;
use App\Offer;

class ItemObserver
{
    public function deleting(Item $item)
    {
        $auction_item = AuctionItem::where('item_id', $item->id)->first();
        $offers = Offer::where('auction_item_id', $auction_item->id)->get();
        foreach ($offers as $offer) {
            $offer->delete();
        }
        $auction_item->delete();
        $auction_users = AuctionUser::where('item_id', $item->id)->get();
        foreach ($auction_users as $auction_user) {
            $auction_user->delete();
        }
        $notifications = Notification::whereIn('item_id', $item->id)->get();
        foreach ($notifications as $notification) {
            $notification->delete();
        }
    }
}
