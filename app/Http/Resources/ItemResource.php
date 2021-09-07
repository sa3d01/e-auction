<?php

namespace App\Http\Resources;

use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use App\Setting;
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
        $auction_item=AuctionItem::where('item_id',$this->id)->orderBy('created_at','desc')->first();
        $is_favourite=false;
        $my_item=false;
        $win=false;
        $can_bid=true;


        if (\request()->user()){
            //favourite
            $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$this->id])->first();
            if ($favourite){
                $is_favourite=true;
            }
            if ($this->user_id==\request()->user()->id){
                $my_item=true;
            }
            $soon_winner=AuctionUser::where('item_id',$this->id)->latest()->value('user_id');
            if ($soon_winner){
                if ($soon_winner==\request()->user()->id){
                    $win=true;
                }
            }
            if ($auction_item){
                $features=$auction_item->auctionTypeFeatures(auth()->user()->id);
            }
        }else{
            if ($auction_item){
                $features=$auction_item->auctionTypeFeatures();
            }
        }
        //status
        if (!$auction_item){
            $auction_status=$this->status;
            $negotiation=false;
            $direct_pay=false;
            $user_price=$this->price??0;
            $bid_count=0;
        }else{
            $auction_status=$features['status'];
            $negotiation=$features['negotiation'];
            $direct_pay=$features['direct_pay'];
            $user_price=$features['user_price'];
            $bid_count=(int)AuctionUser::where(['auction_id'=>$auction_item->auction_id,'item_id'=>$auction_item->item_id])->count();
            $now=Carbon::now();
            $bid_pause_period=Setting::value('bid_pause_period');
            if ($auction_item->more_details['status'] == 'soon' && ($now->diffInSeconds(Carbon::createFromTimestamp($auction_item->auction->start_date))) < $bid_pause_period){
                $can_bid=false;
            }
        }


        return [
            'id'=> (int) $this->id,
            'images'=> $this->images,
            'start_date'=> $auction_item?$auction_item->auction->start_date:123,
            'start_date_text'=> Carbon::createFromTimestamp($auction_item?$auction_item->auction->start_date:123),
            'auction_duration'=>$auction_item?$auction_item->auction->duration:0,
            'item_status'=> $this->item_status->name[$this->lang()],
            'auction_price'=> $auction_item?$auction_item->price:($this->price??0),
            'bid_count'=>(int)$bid_count,
            'name'=>$this->year.' '. $this->mark->name[$this->lang()].' '.$this->model->name[$this->lang()],
            'city'=> $this->city->name[$this->lang()],
            'mark'=> $this->mark->name[$this->lang()],
            'model'=> $this->model->name[$this->lang()],
            'year'=> $this->year??0,
            'fetes'=> $this->fetes->name[$this->lang()],
            'kms_count'=> $this->kms_count,
            'color'=> $this->color?$this->color->name[$this->lang()]:"",
            'sunder_count'=> $this->sunder_count,
            'auction_type'=> $this->auction_type->name['ar'],
            'is_favourite'=> $is_favourite,
            'auction_status'=>$auction_status,
            'negotiation'=>$negotiation,
            'direct_pay'=>$direct_pay,
            'user_price'=>$user_price,
            'my_item'=>$my_item,
            'tax'=> $this->tax=='true'?true:false,
            'win'=>$win,
            'can_bid'=>$can_bid
        ];
    }
}
