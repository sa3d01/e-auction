<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AuctionItem extends Model
{
    use ModelBaseFunctions;

    private $route='auction_item';
    private $images_link='media/images/auction_item/';
    protected $fillable = ['more_details','start_date','latest_charge','price','auction_id','item_id','vip'];
    protected $casts = [
        'more_details' => 'json',
    ];
    public function item():object{
        return $this->belongsTo(Item::class);
    }
    public function auction():object{
        return $this->belongsTo(Auction::class);
    }
    protected function setStartDateAttribute($start_date)
    {
        $this->attributes['start_date'] = $start_date;
    }
    public function auctionTypeFeatures($authed_user_id=null):array{
        $arr['negotiation']=false;
        $arr['direct_pay']=false;
        $arr['user_price'] = "";
        $arr['live'] = false;
        $arr['status'] = $this->more_details['status'];
        $start_auction = Carbon::createFromTimestamp($this->auction->start_date);
        if (($this->item->auction_type_id == 4) || ($this->item->auction_type_id == 3)) {
            $arr['user_price'] = $this->item->price;
        }
        if (($this->more_details['status']!='paid') && ($this->more_details['status']!='expired') && ($this->more_details['status']!='negotiation') && ($this->more_details['status']!='delivered')) {
            $end_date=Carbon::createFromTimestamp($this->start_date)->addSeconds($this->auction->duration);
            $now=Carbon::now();
            if ($now->between($start_auction, $end_date)) {
                $arr['live'] = true;
                $arr['status'] = 'live';
                $this_offers=Offer::where('auction_item_id',$this->id)->get();
                foreach ($this_offers as $this_offer){
                    $this_offer->delete();
                }
            }
            if ($this->item->auction_type_id == 4) {
                //البيع المباشر
                if ($this->more_details['status']=='soon') {
                    $arr['negotiation'] = true;
                    $arr['direct_pay'] = true;
                }
            }
        }
        return $arr;
    }
}
