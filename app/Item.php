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
        'user_id','category_id','mark_id','model_id','item_status_id','city_id','sale_type_id'
        ,'images','location'
        ,'paper_image','price','shipping_by','status','image','name','model_class','factory','kms'
    ];
    protected $casts = [
        'images' => 'array',
        'location' => 'json',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function sale_type(){
        return $this->belongsTo(SaleType::class);
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
}
