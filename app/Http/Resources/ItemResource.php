<?php

namespace App\Http\Resources;

use App\AuctionItem;
use App\Favourite;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    function lang(){
        if (\request()->header('lang')){
            return \request()->header('lang');
        }else{
            return 'ar';
        }
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $auction_item=AuctionItem::where('item_id',$this->id)->latest()->first();
        $end_auction=Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration)->setTimezone('Africa/Cairo');
        $start_auction=Carbon::createFromTimestamp($auction_item->start_date)->setTimezone('Africa/Cairo');
        if ($end_auction < Carbon::now('Africa/Cairo')){
            $auction_status='expired';
            $auction_item->update([
               'more_details'=>[
                   'status'=>'expired'
               ]
            ]);
        }elseif (($start_auction <= Carbon::now('Africa/Cairo') )  &&  ($end_auction >= Carbon::now('Africa/Cairo'))){
            $auction_status='live';
        }else{
            $auction_status='soon';
        }
        $is_favourite=false;
        if (\request()->user()){
            $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$this->id])->first();
            if ($favourite){
                $is_favourite=true;
            }
        }
        return [
            'id'=> (int) $this->id,
            'images'=> $this->images,
            'start_date'=> $auction_item->start_date,
            'start_date_text'=> Carbon::createFromTimestamp($auction_item->start_date),
            'auction_duration'=>$auction_item->auction->duration,
            'item_status'=> $this->item_status->name[$this->lang()],
            'auction_price'=> $auction_item->price,
            'name'=> $this->name,
            'city'=> $this->city->name[$this->lang()],
            'mark'=> $this->mark->name[$this->lang()],
            'model'=> $this->model->name[$this->lang()],
            'fetes'=> $this->fetes->name[$this->lang()],
            'kms_count'=> $this->kms_count,
            'color'=> $this->color->name[$this->lang()],
            'sunder_count'=> $this->sunder_count,
            'auction_type'=> $this->auction_type->name[$this->lang()],
            'is_favourite'=> $is_favourite,
            'auction_status'=>$auction_status,
        ];
    }
}
