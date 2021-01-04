<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class AuctionItem extends Model
{
    use ModelBaseFunctions;

    private $route='auction_item';
    private $images_link='media/images/auction_item/';
    protected $fillable = ['more_details','start_date','latest_charge','price','auction_id','item_id'];
    protected $casts = [
        'more_details' => 'json',
    ];
    public function item(){
        return $this->belongsTo(Item::class);
    }
    public function auction(){
        return $this->belongsTo(Auction::class);
    }
}
