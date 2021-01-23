<?php

namespace App\Http\Resources;

use App\Auction;
use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
{
    function lang(){
        if (\request()->header('lang')){
            return \request()->header('lang');
        }else{
            return 'ar';
        }
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
            $my_item=false;
            $win=false;
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
            if ($auction_item){
                if (\request()->user()){
                    $features=$auction_item->auctionTypeFeatures(auth()->user()->id);
                    if ($auction_item->more_details['status']=='paid'){
                        $winner=AuctionUser::where('item_id',$obj->id)->latest()->value('user_id');
                        if ($winner==\request()->user()->id){
                            $win=true;
                        }
                    }
                }else{
                    $features=$auction_item->auctionTypeFeatures();
                }
                $arr['auction_status']=$features['status'];
                $arr['negotiation']=$features['negotiation'];
                $arr['direct_pay']=$features['direct_pay'];
                $arr['user_price']=$features['user_price'];
                $arr['live']=$features['live'];

                $arr['auction_type']= $obj->auction_type->name[$this->lang()];
                $arr['start_date']= $auction_item->auction->start_date;
//                $arr['start_date_text']= Carbon::createFromTimestamp($auction_item->start_date)->format('Y-m-d h:i:s A');
                $arr['now_date']= Carbon::now()->format('Y-m-d h:i:s A');
                $arr['end_string_date']=Carbon::createFromTimestamp($auction_item->auction->more_details['end_date'])->format('Y-m-d h:i:s A');
                $arr['start_string_date']=Carbon::createFromTimestamp($auction_item->auction->start_date)->format('Y-m-d h:i:s A');


                $arr['auction_duration']=$auction_item->auction->duration;
                $arr['auction_price']=$auction_item->price;
            }
            $arr['id']=(int)$obj->id;
            $arr['name']=$obj->mark->name[$this->lang()].' '.$obj->model->name[$this->lang()];
            $arr['item_status']= $obj->item_status->name[$this->lang()];
            $arr['city']= $obj->city->name[$this->lang()];
            $arr['image']=$obj->images[0];
            $arr['is_favourite']=$is_favourite;
            $arr['win']=$win;
            $arr["my_item"]=$my_item;
            $data[]=$arr;
        }
        return $data;
    }
}
