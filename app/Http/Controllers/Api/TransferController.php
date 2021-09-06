<?php

namespace App\Http\Controllers\Api;

use App\AuctionItem;
use App\AuctionUser;
use App\Http\Resources\UserResource;
use App\Offer;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransferController extends MasterController
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
        parent::__construct();
    }
    private function upload_file($file){
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move('media/images/transfer/', $filename);
        return $filename;
    }
    public function transfer(Request $request)
    {
        $user = auth()->user();
        if ($user->profileIsFilled() == false) {
            $ar_msg='يجب اكمال بيانات ملفك الشخصى أولا ';
            $en_msg='please complete your profile , and charge your purchasing power';
            return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
        }
        $data = $request->all();
        $data['user_id'] = $user->id;
        if (Transfer::where(['user_id'=>$user->id,'status'=>0,'purchasing_type'=>'bank'])->latest()->first()){
            return $this->sendError('يرجى انتظار رد الإدارة على تحويلك السابق');
        }
        if ($request['type'] == 'purchasing_power' && $request['purchasing_type'] == 'online') {
            $data['purchasing_type']='online';
            Transfer::create($data);
            $user->update(['purchasing_power' =>$user->purchasing_power+ $request['money']]);
        }else{
            $data['purchasing_type']='bank';
            $filename = $this->upload_file($request->file('image'));
            $data['more_details']=[
                'image'=>$filename,
            ];
            Transfer::create($data);
        }
//        if ($request['type']=='buy_item'){
//            if (Transfer::where('more_details->item_id',$request['item_id'])->where('type','buy_item')->where('status',0)->latest()->first()){
//                return $this->sendError('يرجى انتظار رد الإدارة على تحويلك السابق');
//            }
//            $data['purchasing_type']='bank';
//            $filename = $this->upload_file($request->file('image'));
//            $data['more_details']=[
//                'item_id'=>$request['item_id'],
//                'image'=>$filename,
//            ];
//            Transfer::create($data);
//        }else{
//            if ($request['type'] == 'package') {
//                Package::findOrFail($request['package_id']);
//                Transfer::create($data);
//                $user->update(['package_id' => $request['package_id'], 'package_subscribed_at' => Carbon::now()]);
//            } elseif ($request['type'] == 'purchasing_power') {
//                Transfer::create($data);
//                $user->update(['purchasing_power' =>$user->purchasing_power+ $request['money']]);
//            } elseif ($request['type'] == 'wallet') {
//                Transfer::create($data);
//                $add_item_tax = Setting::first()->value('add_item_tax');
//                $wallet = $user->wallet + $request['money'];
//                foreach ($user->items as $item) {
//                    if (($item->pay_status == 0) && ($add_item_tax < $wallet)) {
//                        $item->update(['pay_status' => 1]);
//                        $wallet = $wallet - $add_item_tax;
//                    }
//                }
//                $user->update(['wallet' => $wallet]);
//            }
//        }
        $data = new UserResource($user);
        $token = auth()->login($user);
        return $this->sendResponse($data)->withHeaders(['apiToken' => $token, 'tokenType' => 'bearer']);
    }
    public function refund(Request $request){
        $user = auth()->user();
        if (Transfer::where(['type'=>$request['type'],'status'=>0,'user_id'=>$user->id])->latest()->first()){
            return $this->sendError('يرجى انتظار رد الإدارة على طلبك السابق');
        }
        if ($user->profileIsFilled() == false) {
            $ar_msg='يجب اكمال بيانات ملفك الشخصى أولا ';
            $en_msg='please complete your profile , and charge your purchasing power';
            return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
        }
        if($this->validate_purchasing_power_to_refund($user)==false) {
            $ar_msg='يرجي انتظار انتهاء المزادات التي قمت بالمشاركة بها';
            $en_msg='please wait your auctions to finish first.';
            return $this->sendError($this->lang()=='ar'?$ar_msg:$en_msg);
        }
        $data['user_id']=$user->id;
        $data['money']=$request['money'];
        $data['type']=$request['type'];
        $data['purchasing_type']='bank';
        $data['more_details']=[
            'name'=>$request['name'],
            'account_number'=>$request['account_number'],
            'bank_name'=>$request['bank_name'],
        ];
        Transfer::create($data);
        $data = new UserResource($user);
        return $this->sendResponse($data);
    }
    public function validate_purchasing_power_to_refund($user){
        $user_purchasing_power=$user->purchasing_power+$user->package->purchasing_power_increase;
        $user_purchasing_power=$user_purchasing_power*$this->purchasing_power_ratio;
        //user auction items bids
        $user_items_bids=AuctionUser::where('user_id',$user->id)->pluck('item_id')->toArray();
        $other_auction_items = AuctionItem::whereIn('item_id',$user_items_bids);
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'paid');
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'delivered');
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'expired');
        $other_auction_items = $other_auction_items->where('more_details->status', '!=', 'negotiation')->get();
        if(count($other_auction_items)>0)
        {
            return false;
        }
        //user negotiations
        $offers=Offer::where('sender_id',$user->id)->pluck('auction_item_id')->toArray();
        $other_auction_items = AuctionItem::whereIn('id',$offers);
        $other_auction_items = $other_auction_items->where(function($query) {
            $query->where('more_details->status','negotiation')->orWhere('more_details->status','soon');
        })->get();
        if(count($other_auction_items)>0)
        {
            return false;
        }
        return true;
    }

}
