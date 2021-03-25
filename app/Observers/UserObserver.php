<?php

namespace App\Observers;

use App\AuctionItem;
use App\AuctionUser;
use App\Contact;
use App\FeedBack;
use App\Item;
use App\Notification;
use App\Offer;
use App\Transfer;
use App\User;

class UserObserver
{
    public function deleting(User $user)
    {
        $contacts=Contact::where('user_id',$user->id)->get();
        foreach ($contacts as $contact){
            $contact->delete();
        }
        $feed_backs=FeedBack::where('user_id',$user->id)->get();
        foreach ($feed_backs as $feed_back){
            $feed_back->delete();
        }
        $notifications = Notification::where('receiver_id', $user->id)->get();
        foreach ($notifications as $notification) {
            $notification->delete();
        }
        $transfers = Transfer::where('user_id', $user->id)->get();
        foreach ($transfers as $transfer) {
            $transfer->delete();
        }
        $items=Item::where('user_id',$user->id)->get();
        foreach ($items as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->first();
            if ($auction_item){
                $offers = Offer::where('auction_item_id', $auction_item->id)->get();
                foreach ($offers as $offer) {
                    $offer->delete();
                }
                $auction_item->delete();
            }

            $auction_users = AuctionUser::where('item_id', $item->id)->get();
            foreach ($auction_users as $auction_user) {
                $auction_user->delete();
            }
            $item->delete();
        }
    }
}
