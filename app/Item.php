<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use ModelBaseFunctions;

    private $route='item';
    private $images_link='media/images/item/';
    protected $fillable = [
        'user_id','category_id','status','pay_status'
        ,'name','images','mark_id','model_id','item_status_id','sunder_count','fetes_id',
        'color_id','kms_count','scan_status_id','paper_status_id','paper_image'
        ,'auction_type_id','price','city_id','shipping_by','location'
        ,'more_details'
    ];
    protected $casts = [
        'images' => 'array',
        'location' => 'json',
        'more_details' => 'json',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function auction_type(){
        return $this->belongsTo(AuctionType::class);
    }
    public function category(){
        return $this->belongsTo(DropDown::class,'category_id','id');
    }
    public function mark(){
        return $this->belongsTo(DropDown::class,'mark_id','id');
    }
    public function model(){
        return $this->belongsTo(DropDown::class,'model_id','id');
    }
    public function item_status(){
        return $this->belongsTo(DropDown::class,'item_status_id','id');
    }
    public function city(){
        return $this->belongsTo(DropDown::class,'city_id','id');
    }

    public function fetes(){
        return $this->belongsTo(DropDown::class,'fetes_id','id');
    }
    public function color(){
        return $this->belongsTo(DropDown::class,'color_id','id');
    }
    public function scan_status(){
        return $this->belongsTo(DropDown::class,'scan_status_id','id');
    }
    public function paper_status(){
        return $this->belongsTo(DropDown::class,'paper_status_id','id');
    }

    public function imagesArray(){
        return $this->attributes['images'];
    }
}
