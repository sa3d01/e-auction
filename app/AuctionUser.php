<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class AuctionUser extends Model
{
    use ModelBaseFunctions;

    private $route='auction_user';
    private $images_link='media/images/auction_user/';
    protected $fillable = ['more_details','charge_price','user_id','finish_papers','auction_id','item_id'];
    protected $casts = [
        'more_details' => 'json',
    ];
    public function item(){
        return $this->belongsTo(Item::class);
    }
    public function auction(){
        return $this->belongsTo(Auction::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
