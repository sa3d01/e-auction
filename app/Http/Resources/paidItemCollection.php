<?php

namespace App\Http\Resources;

use App\Auction;
use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class paidItemCollection extends ResourceCollection
{
    function lang(){
        if (\request()->header('lang')){
            return \request()->header('lang');
        }else{
            return 'ar';
        }
    }
    function remain_to_pay($auction_item){
        $where_store_amount=AuctionUser::where(['auction_id'=>$auction_item->auction_id,'item_id'=>$auction_item->item_id])->latest()->first();
        return $where_store_amount->more_details['remain'];
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data=[];
        foreach ($this as $obj){
            $auction_item=AuctionItem::where('item_id',$obj->id)->latest()->first();
            $is_favourite=false;
            if (\request()->user()){
                $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$obj->id])->first();
                if ($favourite){
                    $is_favourite=true;
                }
                if ($obj->user_id==\request()->user()->id){
                    $my_item=true;
                }
            }
            $features=$auction_item->auctionTypeFeatures(auth()->user()->id);
            $arr['auction_status']=$features['status'];
            $arr['negotiation']=false;
            $arr['direct_pay']=false;
            $arr['user_price']=$features['user_price'];
            $arr['live']=false;
            $arr['is_paid']=false;
            if ($features['status']=='paid'){
                $arr['status_text']='فى انتظار الدفع';
                if (Transfer::where('more_details->item_id',$obj->id)->where('type','buy_item')->where('status',0)->latest()->first()){
                    $arr['is_paid']=true;
                    $arr['status_text']='فى انتظار التأكيد';
                }
            }elseif ($features['status']=='delivered'){
                $arr['is_paid']=true;
                $arr['status_text']='تم التسليم';
            }
            $arr['auction_type']= $obj->auction_type->name[$this->lang()];
            $arr['start_date']= $auction_item->auction->start_date;
            $arr['auction_duration']=$auction_item->auction->duration;
            $arr['auction_price']=$this->remain_to_pay($auction_item);

            $arr['id']=(int)$obj->id;
            $arr['name']=$obj->mark->name[$this->lang()].' '.$obj->model->name[$this->lang()];
            $arr['item_status']= $obj->item_status->name[$this->lang()];
            $arr['city']= $obj->city->name[$this->lang()];
            $arr['image']=$obj->images[0];
            $arr['is_favourite']=$is_favourite;
            $arr['win']=true;
            $arr["my_item"]=false;
            $data[]=$arr;
        }
        return $data;
    }
}
