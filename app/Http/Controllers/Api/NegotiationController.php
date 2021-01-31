<?php

namespace App\Http\Controllers\Api;

use App\AuctionItem;
use App\AuctionUser;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
use App\Offer;
use App\Setting;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;

class NegotiationController extends MasterController
{
    protected $model;

    public function __construct(Offer $model)
    {
        $this->model = $model;
        $this->purchasing_power_ratio = Setting::first()->value('purchasing_power_ratio');
        parent::__construct();
    }

    public function directPay($item_id, Request $request):object
    {
        $user = $request->user();
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        if ($auction_item->more_details['status'] == 'expired' || $auction_item->more_details['status'] == 'paid') {
            return $this->sendError('هذا السلعة قد انتهى وقت المزايدة عليها :(');
        }
        $latest_auction_user = AuctionUser::where('item_id', $item_id)->latest()->first();
        if ($latest_auction_user) {
            $charge_price = $auction_item->item->price - $auction_item->price;
        } else {
            $charge_price = $auction_item->item->price;
        }
        AuctionUser::create([
            'user_id' => $user->id,
            'item_id' => $item_id,
            'auction_id' => $auction_item->auction_id,
            'charge_price' => $charge_price
        ]);
        $auction_item->update([
            'price' => $auction_item->item->price,
            'latest_charge' => $charge_price,
            'more_details' => [
                'status' => 'paid',
                'pay_type' => 'direct_pay'
            ]
        ]);
        $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
        $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
        $admin_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
        $this->base_notify($winner_title, $user->id, $auction_item->item_id);
        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
        $this->notify_admin($admin_title, $auction_item);
        return $this->sendResponse('تمت العملية بنجاح');
    }

    public function sendOffer($item_id, Request $request):object
    {
        $item = Item::find($item_id);
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        $sender = $request->user();
        if ($sender->id == $item->user_id) {
            $latest_offer = Offer::where('auction_item_id', $auction_item->id)->latest()->first();
            if ($latest_offer->sender_id == $sender->id) {
                $receiver = User::find($latest_offer->receiver_id);
            } else {
                $receiver = User::find($latest_offer->sender_id);
            }
        } else {
            $receiver = User::find($item->user_id);
        }
        $pending_offer = Offer::where(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'auction_item_id' => $auction_item->id, 'status' => 'pending'])->latest()->first();
        if ($pending_offer) {
            return $this->sendError('لم يتم الرد على عرضك الأخير');
        }
        if ($item->price != null) {
            if ($item->price < $request['price']) {
                return $this->sendError('عرض السعر المقدم أعلى من السعر المحدد من المالك');
            }
        }
        $offers = Offer::where(['auction_item_id' => $auction_item->id, 'receiver_id' => $receiver->id])->orWhere(['auction_item_id' => $auction_item->id, 'sender_id' => $receiver->id])->latest()->get();
        foreach ($offers as $old_offer) {
            $old_offer->update([
                'status' => 'opposite'
            ]);
        }
        $offer = Offer::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'auction_item_id' => $auction_item->id,
            'price' => $request['price'],
            'status' => 'pending'
        ]);
        $this->new_offer_notify($offer);
        return $this->sendResponse('تم الإرسال بنجاح');
    }

    public function acceptOffer($item_id, $offer_id, Request $request): string
    {
        $user = $request->user();
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        if ($auction_item->more_details['status'] == 'expired' || $auction_item->more_details['status'] == 'paid') {
            return $this->sendError('هذا السلعة قد انتهى وقت المزايدة عليها :(');
        }
        $offer = Offer::find($offer_id);
        $charge_price = $offer->price;
        AuctionUser::create([
            'user_id' => $offer->sender_id,
            'item_id' => $item_id,
            'auction_id' => $auction_item->auction_id,
            'charge_price' => $charge_price
        ]);
        $auction_item->update([
            'price' => $offer->price,
            'latest_charge' => $charge_price,
            'more_details' => [
                'status' => 'paid',
                'pay_type' => 'negotiation'
            ]
        ]);
        $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
        $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
        $admin_title['ar'] = 'تم بيع السلعة رقم ' . $auction_item->item_id;
        $this->base_notify($winner_title, $offer->sender_id, $auction_item->item_id);
        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
        $this->notify_admin($admin_title, $auction_item);
        $offers = Offer::where('auction_item_id', $auction_item->id)->get();
        foreach ($offers as $offer) {
            $offer->delete();
        }
        return $this->sendResponse('تمت العملية بنجاح');
    }

    public function refuseOffer($item_id, Request $request):object
    {
        $user = $request->user();
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        $item = Item::find($item_id);
        if (($user->id == $item->user_id) && ($auction_item->more_details['status'] == 'negotiation')) {
            $item->update([
                'status' => 'accepted',
                'reason' => 'resale'
            ]);
            $auction_item->delete();
            $notifications = Notification::where('item_id', $item_id)->get();
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        } else {
            $opposite_offer = Offer::where('auction_item_id', $auction_item->id)->where('status', 'opposite')->latest()->first();
            if ($opposite_offer) {
                $opposite_offer->update([
                    'status' => 'pending'
                ]);
            }
        }
        $latest_offer = Offer::where('auction_item_id', $auction_item->id)->latest()->first();
        $latest_offer->update([
            'status' => 'rejected'
        ]);
        if ($latest_offer->sender_id == $user->id) {
            $receiver = User::find($latest_offer->receiver_id);
        } else {
            $receiver = User::find($latest_offer->sender_id);
        }
        $title['ar'] = 'لقد تم رفض عرض السعر المقدم من قبلك على المزاد رقم ' . $auction_item->item_id;
        $data = [];
        $data['title'] = $title;
        $data['note'] = $title;
        $data['receiver_id'] = $receiver->id;
        $data['item_id'] = $auction_item->item_id;
        Notification::create($data);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $title['ar'],
                'status' => 'refuse_offer',
                'type' => 'refuse_offer',
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($receiver->device['id'])
            ->send();
        return $this->sendResponse('تمت العملية بنجاح');
    }

    public function itemOffers($item_id): object
    {
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        if (!$auction_item) {
            return $this->sendError('توجد مشكله ما');
        }
        $offers = Offer::where(['auction_item_id' => $auction_item->id])->where('status', 'pending')->latest()->get();
        $data = [];
        foreach ($offers as $offer) {
            $arr['id'] = $offer->id;
            $arr['price'] = $offer->price;
            $arr['user_id'] = $offer->sender_id;
            $arr['item'] = new ItemResource(Item::find($item_id));
            if ($offer->sender_id==auth()->user()->id && $offer->status=='pending'){
                $arr['replied']=false;
            }else{
                $arr['replied']=true;
            }
            $data[] = $arr;
        }
        return $this->sendResponse($data);
    }

    public function new_offer_notify($offer)
    {
        $title['ar'] = 'تم إرسال عرض اليك على المزاد رقم ' . $offer->auction_item->item_id;
        $title['en'] = 'تم إرسال عرض اليك على المزاد رقم ' . $offer->auction_item->item_id;
        $data = [];
        $data['title'] = $title;
        $data['note'] = $title;
        $data['receiver_id'] = $offer->receiver_id;
        $data['item_id'] = $offer->auction_item->item_id;
        $data['more_details'] = ['offer_id' => $offer->id];
        Notification::create($data);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $title['ar'],
                'status' => $offer->status,
                'type' => 'offer',
                'item' => new ItemResource(Item::find($offer->auction_item->item_id)),
                'offer_id' => $offer->id
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($offer->receiver->device['id'])
            ->send();
    }

    public function myNegotiationItems():object{
        $my_negotiations_auction_items = Offer::where(['sender_id' => \request()->user(), 'status' => 'pending'])->orWhere(['receiver_id' => \request()->user(), 'status' => 'pending'])->pluck('auction_item_id');
        $item_ids=AuctionItem::whereIn('id',$my_negotiations_auction_items)->pluck('item_id');
        return $this->sendResponse(new ItemCollection(Item::whereIn('id',$item_ids)->latest()->get()));
    }

}
