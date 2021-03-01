<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Carbon\Carbon;
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
        ,'auction_type_id','price','auction_price','city_id','shipping_by','tax','location'
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
    public function reports(){
        return $this->hasMany(Report::class,'item_id','id');
    }
    public function imagesArray(){
        return $this->attributes['images'];
    }
    public function reportLabel()
    {
        $count=$this->reports()->count();
        $route=route('admin.item.reports',$this->id);
        return"<a  href='$route' class='badge badge-success-inverted'>
                $count
                </a>";
    }
    public function auctionPriceLabel()
    {
        $route=route('admin.item.auction_price',$this->id);
        $auction_price=$this->attributes['auction_price'];
        if ($auction_price==null){
            return"<a data-id='$this->id'  data-href='$route' href='$route' class='auction_price badge badge-warning'><i class='os-icon os-icon-map-pin'></i><span>غير محدد </span></a>";
        }else{
            return"<a data-id='$this->id'  data-href='$route' href='$route' class='auction_price badge badge-info'><span> $auction_price  </span></a>";
        }
    }
    public function adminImagesLabel()
    {
        $images=json_decode($this->imagesArray());
        $arr_images=[];
        foreach ($images as $image){
            $arr_images[]="<img style='pointer-events: none;max-height: 100px;max-width: 100px;border-radius: 10px;' src='$image'>";
        }
        $string_images=implode("'\'",$arr_images);
        return"<a data-id='$this->id' style='cursor: pointer' data-toggle='modal' data-target='#uploadImagesModal-$this->id' class='images nav-link'>$string_images</a>";
    }
    public function vip()
    {
        $auction_item=AuctionItem::where('item_id',$this->id)->latest()->first();
        if (!$auction_item){
            return"";
        }
        $action = route('admin.item_vip.update', [$this->attributes['id']]);
        if ($auction_item->vip === "true"){
            return "<a class='block btn btn-success btn-sm' data-href='$action' href='$action'><i class='os-icon os-icon-check-circle'></i><span>سلعة مميزة | الغاء </span></a>";
        }else{
            return "<a class='block btn btn-info btn-sm' data-href='$action' href='$action'><i class='os-icon os-icon-activity'></i><span>تمميز السلعة !</span></a>";
        }
    }
    public function nameForSelect(){
        return $this->id.'-'.$this->model->name['ar'].'-'.$this->auction_type->name['ar'];
    }
    public function itemStatusIcon()
    {
        $auction_item=AuctionItem::where('item_id',$this->id)->latest()->first();
        if(!$auction_item){
            $name= 'فى انتظار الاعداد لمزاد';
            $key = 'info';
        }else{
            $end_auction=Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration);
            $start_auction=Carbon::createFromTimestamp($auction_item->start_date);
            if ($end_auction < Carbon::now()){
                if (is_array($auction_item->more_details)){
                    if ($auction_item->more_details['status']=='paid') {
                        $name= 'سلعة مباعة';
                        $key = 'success';
                    }elseif ($auction_item->more_details['status']=='negotiation'){
                        $name= 'انتهت المزايدة و جارى المفاوضة عليها';
                        $key = 'warning';
                    }else{
                        $name= 'انتهت المزايدة ولم يتم البيع';
                        $key = 'danger';
                    }
                }else{
                    $name= 'انتهت المزايدة ولم يتم البيع';
                    $key = 'danger';
                }
            }elseif (($start_auction <= Carbon::now())  &&  ($end_auction >= Carbon::now())){
                $name= 'بمزاد مباشر';
                $key = 'info';
            }else{
                $name= 'بمزاد قبل مباشر';
                $key = 'info';
            }
        }
        return "<a class='badge badge-$key-inverted'>
                $name
                </a>";
    }
}
