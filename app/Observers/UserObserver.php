<?php

namespace App\Observers;

use App\AuctionItem;
use App\AuctionUser;
use App\Item;
use App\Notification;
use App\Offer;
use App\User;

class UserObserver
{
    public function deleting(User $user)
    {
        $items=Item::where('user_id',$user->id)->get();
        foreach ($items as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->first();
            $auction_item->delete();
            $offers = Offer::where('auction_item_id', $auction_item->id)->get();
            foreach ($offers as $offer) {
                $offer->delete();
            }
            $auction_users = AuctionUser::where('item_id', $item->id)->get();
            foreach ($auction_users as $auction_user) {
                $auction_user->delete();
            }
            $notifications = Notification::where('receiver_id', $user->id)->get();
            foreach ($notifications as $notification) {
                $notification->delete();
            }
            $item->delete();
        }
    }
}
