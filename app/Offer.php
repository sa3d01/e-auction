<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use ModelBaseFunctions;

    private $route='offer';
    private $images_link='media/images/offer/';
    protected $fillable = [
        'sender_id','receiver_id','auction_item_id','price','status'
        ,'more_details'
    ];
    protected $casts = [
        'more_details' => 'json',
    ];
    public function sender(){
        return $this->belongsTo(User::class,'sender_id','id');
    }
    public function receiver(){
        return $this->belongsTo(User::class,'receiver_id','id');
    }
    public function auction_item(){
        return $this->belongsTo(AuctionItem::class,'auction_item_id','id');
    }
}
