<?php

namespace App\Http\Controllers\Api;

use App\AuctionItem;
use App\AuctionUser;
use App\Http\Controllers\Controller;
use App\Offer;
use App\Setting;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class MasterController extends Controller
{
    protected $model;
    protected $auth_key;
    protected $purchasing_power_ratio;
    public function __construct()
    {
        $this->auctionItemStatusUpdate();
        $this->purchasing_power_ratio = Setting::first()->value('purchasing_power_ratio');
        parent::__construct();
    }
    function lang(){
        if (\request()->header('lang')){
            return \request()->header('lang');
        }else{
            return 'ar';
        }
    }
    public function sendResponse($result)
    {
        $response = [
            'status' => 200,
            'data' => $result,
        ];
        return response()->json($response);
    }

    public function sendError($error, $code = 400)
    {
        $response = [
            'status' => $code,
            'message' => $error,
        ];
        return response()->json($response);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),$this->validation_rules(1),$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data=$request->all();
        try {
            $data['user_id']=auth()->user()->id;
        }catch (UserNotDefinedException $e){

        }
        $this->model->create($data);
        return $this->sendResponse('تم الانشاء بنجاح');
    }
    public function validate_purchasing_power($user,$price,$auction_item){
        $user_purchasing_power=($user->purchasing_power+$user->package->purchasing_power_increase)*$this->purchasing_power_ratio;
        //user auction items bids
        $user_items_bids=AuctionUser::where('user_id',$user->id)->pluck('item_id')->toArray();
        $other_auction_items = AuctionItem::whereIn('item_id',$user_items_bids);
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'paid');
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'delivered');
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'expired');
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'negotiation')->get();
        foreach ($other_auction_items as $other_auction_item)
        {
            if ($other_auction_item->id != $auction_item->id){
                $soon_winner = AuctionUser::where(['item_id'=>$other_auction_item->item_id,'auction_id'=>$other_auction_item->auction_id])->latest()->value('user_id');
                if ($soon_winner == $user->id){
                    $user_purchasing_power=$user_purchasing_power-$other_auction_item->price;
                }
            }
        }
        //user negotiations
        $offers=Offer::where('sender_id',$user->id)->pluck('auction_item_id')->toArray();
        $other_auction_items = AuctionItem::whereIn('id',$offers);
        $other_auction_items = $other_auction_items->where(function($query) {
            $query->where('more_details->status','negotiation')->orWhere('more_details->status','soon');
        })->get();
        foreach ($other_auction_items as $other_auction_item)
        {
            if ($other_auction_item->id != $auction_item->id){
                $offer_price=Offer::where(['sender_id'=>$user->id,'auction_item_id'=>$other_auction_item->id,'status'=>'pending'])->latest()->value('price');
                if ($offer_price){
                    $user_purchasing_power=$user_purchasing_power-$offer_price;
                }
            }
        }
        //check
        if ($user_purchasing_power < $price){
            $ar_msg='عذرا رصيدك لايكفي للمزايدة. يرجى شحن العربون';
            $en_msg=' Sorry, your purchasing power is not enough to bid !';
            return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
        }
        if (Transfer::where(['user_id'=>$user->id,'type'=>'refund_purchasing_power','status'=>0])->first()){
            $ar_msg=' قوتك الشرائية معلقة حاليا لحين رد الإدارة';
            $en_msg=' your purchasing power is paused for now';
            return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
        }
        return true;
    }
    function checkTimeForBid($auction_item,$bid_time)
    {
        if ($auction_item->more_details != null) {
           // $now=Carbon::now();
            $now=Carbon::createFromTimestamp($bid_time);
            return $this->sendError($now);

            $bid_pause_period=Setting::value('bid_pause_period');
            if ($auction_item->more_details['status'] == 'expired' || $auction_item->more_details['status'] == 'paid') {
                $ar_msg='هذه المركبة قد انتهى وقت المزايدة عليها :(';
                $en_msg='timout auction :(';
                return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
            }elseif ($auction_item->more_details['status'] == 'soon' && ($now->diffInSeconds(Carbon::createFromTimestamp($auction_item->auction->start_date),false)) < $bid_pause_period){
                $ar_msg='يرجى الانتظار لبداية المزاد المباشر';
                $en_msg='please wait to start auction time';
                return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
            }
            return true;
        }
        return true;
    }
    function checkCompletedProfile($user){
        if ($user->profileAndPurchasingPowerIsFilled() == false) {
            $ar_msg='يجب اكمال بيانات ملفك الشخصى أولا وشحن قوتك الشرائية';
            $en_msg='please complete your profile , and charge your purchasing power';
            return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
        }
        return true;
    }
    function canBid($user,$auction_item,$total_price,$bid_time)
    {
        if ($total_price <= $auction_item->price){
            return $this->sendError('لا يمكن المزايدة بأقل من القيمة الحالية للمزاد');
        }
        if ($this->checkTimeForBid($auction_item,$bid_time) !== true)
        {
            return $this->checkTimeForBid($auction_item,$bid_time);
        }
        if ($this->validate_purchasing_power($user, $total_price,$auction_item) !== true)
        {
            return $this->validate_purchasing_power($user, $total_price,$auction_item);
        }
        if ($this->checkCompletedProfile($user) !== true)
        {
            return $this->checkCompletedProfile($user);
        }
        return true;
    }

}
