<?php

namespace App\Http\Resources;

use App\Auction;
use App\AuctionItem;
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
            $is_favourite=false;
            if (\request()->user()){
                $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$obj->id])->first();
                if ($favourite){
                    $is_favourite=true;
                }
            }
            if ($auction_item){
                if (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) < Carbon::now(date_default_timezone_get())){
                    $arr['auction_status']='expired';
                    $auction_item->update([
                        'more_details'=>[
                            'status'=>'expired'
                        ]
                    ]);
                }elseif ((Carbon::createFromTimestamp($auction_item->start_date) <= Carbon::now(date_default_timezone_get()) )  &&  (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) >= Carbon::now(date_default_timezone_get()))){
                    $arr['auction_status']='live';
                }else{
                    $arr['auction_status']='soon';
                }
                $arr['auction_type']= $obj->auction_type->name[$this->lang()];
                $arr['start_date']= $auction_item->start_date;
                $arr['start_date_text']= Carbon::createFromTimestamp($auction_item->start_date)->format('Y-m-d H:i');
                $arr['now_date']= Carbon::now(date_default_timezone_get())->format('Y-m-d H:i');
                $arr['auction_duration']=$auction_item->auction->duration;
                $arr['auction_price']=$auction_item->price;
            }
            $arr['id']=(int)$obj->id;
            $arr['name']=$obj->name;
            $arr['item_status']= $obj->item_status->name[$this->lang()];
            $arr['image']=$obj->images[0];
            $arr['is_favourite']=$is_favourite;
            $data[]=$arr;
        }
        return $data;
    }
}
