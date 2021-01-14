<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use ModelBaseFunctions;

    private $route='auction';
    private $images_link='media/images/auction/';
    protected $fillable = ['items','start_date','duration','auction_type_id','more_details'];
    protected $casts = [
        'items' => 'array',
        'more_details' => 'json',
    ];
    public function auction_type(){
        return $this->belongsTo(AuctionType::class);
    }

    public function auctionStatus()
    {
        if (Carbon::createFromTimestamp($this->more_details['end_date']) < Carbon::now()){
            return"<span> مزاد منتهى  </span>";
        }elseif ((Carbon::createFromTimestamp($this->start_date) <= Carbon::now() )  &&  (Carbon::createFromTimestamp($this->more_details['end_date']) >= Carbon::now())){
            return"<span> مزاد مباشر  </span>";
        }else{
            return"<span> مزاد قبل مباشر  </span>";
        }
    }
}
