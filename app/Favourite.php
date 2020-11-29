<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use ModelBaseFunctions;

    private $route='favourite';
    private $images_link='media/images/favourite/';
    protected $fillable = ['user_id','auction_id','item_id'];

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
