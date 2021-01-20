<?php

namespace App\Observers;

use App\Auction;
use App\AuctionItem;
use App\AuctionUser;
use App\Item;
use App\Notification;
use App\Offer;

class AuctionObserver
{
    public function deleting(Auction $auction)
    {
        $auction_items=AuctionItem::where('auction_id',$auction->id)->get();
        $item_ids=AuctionItem::where('auction_id',$auction->id)->pluck('item_id');
        foreach ($auction_items as $auction_item){
            $item=Item::find($auction_item->item_id);
            $item->update([
               'status'=>'accepted'
            ]);
            $auction_item->delete();
            $offers=Offer::where('auction_item_id',$auction_item->id)->get();
            foreach ($offers as $offer){
                $offer->delete();
            }
        }

        $auction_users=AuctionUser::where('auction_id',$auction->id)->get();
        foreach ($auction_users as $auction_user){
            $auction_user->delete();
        }

        $notifications=Notification::whereIn('item_id',$item_ids)->get();
        foreach ($notifications as $notification){
            $notification->delete();
        }

    }
}
