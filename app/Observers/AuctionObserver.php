<?php

namespace App\Observers;

use App\Auction;
use App\AuctionItem;
use App\AuctionUser;
use App\Item;

class AuctionObserver
{
    public function deleting(Auction $auction)
    {
        $auction_items=AuctionItem::where('auction_id',$auction->id)->get();
        foreach ($auction_items as $auction_item){
            $item=Item::find($auction_item->item_id);
            $item->update([
               'status'=>'accepted'
            ]);
            $auction_item->delete();
        }

        $auction_users=AuctionUser::where('auction_id',$auction->id)->get();
        foreach ($auction_users as $auction_user){
            $auction_user->delete();
        }
    }
}