<?php

namespace App\Http\Resources;

use App\Auction;
use App\Favourite;
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
            $auction=Auction::where('items', 'like', '%'.$obj->id.'%')->first();
            $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$obj->id, 'auction_id'=>$auction->id])->first();
            if ($favourite){
                $is_favourite=true;
            }else{
                $is_favourite=false;
            }
            $arr['id']=(int)$obj->id;
            $arr['name']=$obj->name;
            $arr['item_status']= $obj->item_status->name[$this->lang()];
            $arr['auction_type']= $obj->auction_type->name[$this->lang()];
            $arr['start_date']= $auction->start_date;
            $arr['image']=$obj->images[0];
            $arr['auction_price']=$obj->auction_price;
            $arr['is_favourite']=$is_favourite;
            $data[]=$arr;
        }
        return $data;
    }
}