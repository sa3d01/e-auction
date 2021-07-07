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
        parent::__construct();
    }

    public function directPay($item_id, Request $request):object
    {
        $user = $request->user();
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        //validates
        if ($auction_item->more_details['status'] == 'expired' || $auction_item->more_details['status'] == 'paid') {
            $ar_msg='هذه المركبة قد انتهى وقت المزايدة عليها :(';
            $en_msg='timout auction :(';
            return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
        }
        if ($this->checkCompletedProfile($user) !== true)
        {
            return $this->checkCompletedProfile($user);
        }
        if ($this->validate_purchasing_power($user,$auction_item->item->price,$auction_item)!==true){
            return $this->validate_purchasing_power($user,$auction_item->item->price,$auction_item);
        }
        //end-validates
        $latest_auction_user = AuctionUser::where('item_id', $item_id)->latest()->first();
        if ($latest_auction_user) {
            $charge_price = $auction_item->item->price - $auction_item->price;
        } else {
            $charge_price = $auction_item->item->price;
        }
        $auction_user=AuctionUser::create([
            'finish_papers'=>$request->input('finish_papers',0),
            'user_id' => $user->id,
            'item_id' => $item_id,
            'auction_id' => $auction_item->auction_id,
            'charge_price' => $charge_price
        ]);
        //winner
        $auction_item_data=$this->pay($auction_item,$auction_user,$charge_price,$auction_item->item->price,'direct_pay');
        $auction_item->update($auction_item_data);
        //owner
        $this->editWallet($auction_user->item->user,$auction_item->price);
        $winner_title['ar'] = 'تهانينا اليك ! لقد تمت عملية الشراء بنجاح .. مركبة رقم ' . $auction_item->item_id;
        $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
        $admin_title['ar'] = 'تم بيع المركبة رقم ' . $auction_item->item_id;
        $this->base_notify($winner_title, $user->id, $auction_item->item_id);
        $this->base_notify($owner_title, $auction_item->item->user_id, $auction_item->item_id);
        $this->notify_admin($admin_title, $auction_item);
        return $this->sendResponse('تمت العملية بنجاح');
    }
    public function sendOffer($item_id, Request $request):object
    {
        $sender = $request->user();
        $item = Item::find($item_id);
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        $latest_user_offer=Offer::where(['auction_item_id'=>$auction_item->id,'sender_id'=>$sender->id])->latest()->first();
        if($request->user()->id != $item->user_id){
//            الشاري
            if ($sender->profileAndPurchasingPowerIsFilled()==false){
                return $this->sendError(' يجب اكمال بيانات ملفك الشخصى أولا وشحن قوتك الشرائية');
            }
            if ($this->validate_purchasing_power($sender,$request['price'],$auction_item)!==true){
                return $this->validate_purchasing_power($sender,$request['price'],$auction_item);
            }
            if ($latest_user_offer){
                if ($latest_user_offer->price > $request['price']) {
                    return $this->sendError('لا يمكن تقديم عرض سعر أقل من عرض السعر الذى تم تقديمه من قبل!');
                }
            }
            $owner_offer=Offer::where(['auction_item_id'=>$auction_item->id,'sender_id'=>$item->user_id,'receiver_id'=>$request->user()->id])->latest()->first();
            if ($owner_offer){
                if ($owner_offer->price < $request['price']){
                    return $this->sendError('لا يمكن تقديم عرض سعر أعلى من عرض السعر الذى تم تقديمه من قبل البائع!');
                }
            }
        }

        if ($request->has('offer_id') && $request['offer_id']!=null){
            $latest_offer=Offer::find($request['offer_id']);
            if ($sender->id == $item->user_id) {
                if ($latest_offer->price > $request['price']) {
                    return $this->sendError('لا يمكن تقديم عرض سعر أقل من عرض السعر المقدم من المزايد !');
                }
                if ($latest_offer->sender_id == $sender->id) {
                    $receiver = User::find($latest_offer->receiver_id);
                } else {
                    $receiver = User::find($latest_offer->sender_id);
                }
            } else {
                $receiver = User::find($item->user_id);
            }
        }else{
            $receiver = User::find($item->user_id);
        }

        $owner_offer=Offer::where(['auction_item_id'=>$auction_item->id,'sender_id'=>$item->user_id,'receiver_id'=>$receiver->id])->latest()->first();

        if ($owner_offer){
            if ($owner_offer->price < $request['price']) {
                return $this->sendError('لا يمكن تقديم عرض سعر أعلى من عرضك الأخير!');
            }
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
        if (isset($latest_offer)){
            $latest_offer->update([
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
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        if ($auction_item->more_details['status'] == 'expired' || $auction_item->more_details['status'] == 'paid') {
            return $this->sendError('هذا المركبة قد انتهى وقت المزايدة عليها :(');
        }
        $offer = Offer::find($offer_id);
        $charge_price = $offer->price;
        if ($offer->sender_id==$auction_item->item->user_id){
            $auction_user_id=$offer->receiver_id;
        }else{
            $auction_user_id=$offer->sender_id;
        }
        $auction_user=AuctionUser::create([
            'finish_papers'=>$request->input('finish_papers',0),
            'user_id' => $auction_user_id,
            'item_id' => $item_id,
            'auction_id' => $auction_item->auction_id,
            'charge_price' => $charge_price
        ]);
        $auction_user->refresh();
        //winner
        $auction_item_data=$this->pay($auction_item,$auction_user,$charge_price,$offer->price,'negotiation');
        $auction_item->update($auction_item_data);
        //owner
        $this->editWallet($auction_user->item->user,$auction_item->price);
        $winner_title['ar'] = 'تهانينا اليك ! لقد فزت فى المزاد الذى قمت بالمشاركة به رقم ' . $auction_item->item_id;
        $owner_title['ar'] = 'تهانينا اليك ! لقد تم بيع سلعتك بمزاد رقم ' . $auction_item->item_id;
        $admin_title['ar'] = 'تم بيع المركبة رقم ' . $auction_item->item_id;
        $this->base_notify($winner_title, $auction_user_id, $auction_item->item_id);
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
        $refused_offer=Offer::find($request['offer_id']);
        $user = $request->user();
        $auction_item = AuctionItem::where('item_id', $item_id)->latest()->first();
        $item = Item::find($item_id);
//        $senario=[
//            'soon'=>[
//                'owner'=>'المفاوضه تختفى من عند الاتنين بس',
//                'user'=>'رجع للسعر اللى قبله',
//            ],
//            'negotioation'=>[
//                'owner'=>'اعادة تهيئة',
//                'user'=>'السعر اللى قبله',
//            ]
//        ];
////
        if (($user->id == $item->user_id) && ($auction_item->more_details['status'] == 'negotiation')) {
            $item->update([
                'status' => 'accepted',
                'reason' => 'resale'
            ]);
//            $auction_item->delete();
//            $notifications = Notification::where('item_id', $item_id)->get();
//            foreach ($notifications as $notification) {
//                $notification->delete();
//            }
        }elseif($user->id != $item->user_id) {
            $opposite_offer = Offer::where(['auction_item_id'=> $auction_item->id,'sender_id'=>$user->id,'receiver_id'=>$refused_offer->sender_id])->where('status', 'opposite')->latest()->first();
            if ($opposite_offer) {
                $opposite_offer->update([
                    'status' => 'pending'
                ]);
            }
        }
        $refused_offer->update([
            'status' => 'rejected'
        ]);
        if ($refused_offer->sender_id == $user->id) {
            $receiver = User::find($refused_offer->receiver_id);
        } else {
            $receiver = User::find($refused_offer->sender_id);
        }
        $title['ar'] = 'لقد تم رفض عرض السعر المقدم من قبلك على المزاد رقم ' . $auction_item->item_id;
        $data = [];
        $data['title'] = $title;
        $data['note'] = $title;
        $data['receiver_id'] = $receiver->id;
        $data['item_id'] = $auction_item->item_id;
        $data['more_details'] =[
            'offer_id'=>$refused_offer->id
        ];
        Notification::create($data);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $title['ar'], 'sound' => 'default'),
            'data' => [
                'title' => $title['ar'],
                'body' => $title['ar'],
                'status' => 'refuse_offer',
                'type' => 'refuse_offer',
                'db'=>true,
                'offer_id' => $refused_offer->id,
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
        $q_offers=Offer::query();
        $q_offers=$q_offers->where('auction_item_id',$auction_item->id);
        $q_offers=$q_offers->where('status','pending');
        if (\request()->user()->id != $auction_item->item->user_id) {
            $q_offers = $q_offers->where(function($query) {
                $query->where('receiver_id',\request()->user()->id)
                    ->orWhere('sender_id',\request()->user()->id);
            });
        }
        $offers=$q_offers->latest()->get();
        $data = [];
        foreach ($offers as $offer) {
            $q_pre_offer=Offer::query();
            $q_pre_offer=$q_pre_offer->where('auction_item_id',$offer->auction_item_id);
            $q_pre_offer=$q_pre_offer->where('status','!=','pending');
            $q_pre_offer=$q_pre_offer->where(['sender_id'=>$offer->receiver_id,'receiver_id'=>$offer->sender_id]);
            $pre_offer=$q_pre_offer->latest()->first();
            if ($pre_offer){
                $arr['pre_price'] = $pre_offer->price;
            }else{
                $arr['pre_price'] = '';
            }
            $arr['id'] = $offer->id;
            $arr['price'] = $offer->price;
            if ($offer->sender_id==\request()->user()->id){
                $user_offer=User::find($offer->receiver_id);
            }else{
                $user_offer=User::find($offer->sender_id);
            }
            $arr['user_id'] = $user_offer->id;
            $arr['user_name'] = $user_offer->name;
            $arr['user_image'] = $user_offer->image;
            $arr['item'] = new ItemResource(Item::find($item_id));
            if ($offer->sender_id==\request()->user()->id && $offer->status=='pending'){
                $arr['replied']=false;
            }else{
                $arr['replied']=true;
            }
            $arr['my_id']=\request()->user()->id;
            $arr['item_owner_id']=$auction_item->item->user_id;

            if ($offer->sender_id==$auction_item->item->user_id){
                $buyer_offer=Offer::where('auction_item_id',$offer->auction_item_id)->where('sender_id',$offer->receiver_id)->latest()->first();
                $owner_offer=Offer::where('auction_item_id',$offer->auction_item_id)->where('sender_id',$offer->auction_item->item->user_id)->where('receiver_id',$offer->receiver_id)->latest()->first();
            }else{
                $buyer_offer=Offer::where('auction_item_id',$offer->auction_item_id)->where('sender_id',$offer->sender_id)->latest()->first();
                $owner_offer=Offer::where('auction_item_id',$offer->auction_item_id)->where('sender_id',$offer->auction_item->item->user_id)->where('receiver_id',$offer->sender_id)->latest()->first();
            }
            $arr['owner_offer']=$owner_offer->price??"";
            $arr['buyer_offer']=$buyer_offer->price??"";
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
                'db'=>true,
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
        $q_offers=Offer::query();
        $q_offers=$q_offers->where('status','pending');
        $q_offers = $q_offers->where(function($query) {
            $query->where('receiver_id',\request()->user()->id)
                ->orWhere('sender_id',\request()->user()->id);
        });
        $my_negotiations_auction_items = $q_offers->pluck('auction_item_id');
        $item_ids_q=AuctionItem::whereIn('id',$my_negotiations_auction_items);
        $item_ids_q = $item_ids_q->where(function($query) {
            $query->where('more_details->status','negotiation')->orWhere('more_details->status','soon');
        });
        $item_ids=$item_ids_q->pluck('item_id');
        return $this->sendResponse(new ItemCollection(Item::whereIn('id',$item_ids)->latest()->get()));
    }
}
