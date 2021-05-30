<?php

namespace App\Http\Resources;

use App\Auction;
use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use App\Setting;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
{
    function lang(){
        if (\request()->header('lang')){
            return \request()->header('lang');
        }else{
            return 'ar';
        }
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data=[];
        foreach ($this as $obj){
            if ($obj->user->status===0){
                continue;
            }
            $auction_item=AuctionItem::where('item_id',$obj->id);
            $auction=Auction::whereJsonContains('items',$obj->id)->where('more_details->end_date','<',Carbon::now()->timestamp)->latest()->first();
            if ($auction){
                $auction_item=$auction_item->where('auction_id',$auction->id)->latest()->first();
            }else{
                $auction_item=$auction_item->latest()->first();
            }

            $my_item=false;
            $win=false;
            $is_favourite=false;
            if (\request()->user()){
                $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$obj->id])->first();
                if ($favourite){
                    $is_favourite=true;
                }
                if ($obj->user_id==\request()->user()->id){
                    $my_item=true;
                }
            }
            $arr['status_text']='';
            $arr['bid_count']=0;
            $arr['can_bid']=true;
            if ($auction_item){
                if (\request()->user()){
                    $features=$auction_item->auctionTypeFeatures(auth()->user()->id);
                    $soon_winner=AuctionUser::where('item_id',$obj->id)->latest()->value('user_id');
                    if ($soon_winner){
                        if ($soon_winner==\request()->user()->id){
                            $win=true;
                        }
                    }
                }else{
                    $features=$auction_item->auctionTypeFeatures();
                }
                $arr['auction_status']=$features['status'];
                $arr['negotiation']=$features['negotiation'];
                $arr['direct_pay']=$features['direct_pay'];
                $arr['user_price']=$features['user_price'];
                $arr['live']=$features['live'];

                $arr['is_paid']=false;
                if ($features['status']=='paid'){
                    $arr['status_text']='مغلق';
                    if (Transfer::where('more_details->item_id',$obj->id)->where('type','buy_item')->where('status',0)->latest()->first()){
                        $arr['is_paid']=true;
                        $arr['status_text']='مغلق';
                    }
                }elseif ($features['status']=='delivered'){
                    $arr['is_paid']=true;
                    $arr['status_text']='مغلق';
                }elseif ($features['status']=='negotiation'){
                    $arr['is_paid']=true;
                    $arr['status_text']='مغلق';
                }elseif ($features['status']=='soon'){
                    $arr['status_text']='';
                }elseif ($features['status']=='expired'){
                    $arr['status_text']='مغلق';
                }elseif ($features['status']=='live'){
                    $arr['status_text']='مباشر';
                }
                $now=Carbon::now();
                $bid_pause_period=Setting::value('bid_pause_period');
                if ($auction_item->more_details['status'] == 'soon' && ($now->diffInSeconds(Carbon::createFromTimestamp($auction_item->auction->start_date))) < $bid_pause_period){
                    $arr['can_bid']=false;
                }
                $arr['auction_type']= $obj->auction_type->name[$this->lang()];
                $arr['start_date']= $auction_item->auction->start_date;
                $arr['now_date']= Carbon::now()->format('Y-m-d h:i:s A');
                $arr['end_string_date']=Carbon::createFromTimestamp($auction_item->auction->more_details['end_date'])->format('Y-m-d h:i:s A');
                $arr['start_string_date']=Carbon::createFromTimestamp($auction_item->auction->start_date)->format('Y-m-d h:i:s A');
                $arr['auction_duration']=$auction_item->auction->duration;
                $arr['auction_price']=$auction_item->price;
                $arr['bid_count']=(int)AuctionUser::where(['auction_id'=>$auction_item->auction_id,'item_id'=>$auction_item->item_id])->count();
            }else{
                if($obj->status=='pending'){
                    $arr['auction_status']='تم طلب الاضافة';
                    $arr['status_text']='تم طلب الاضافة';
                }elseif ($obj->status=='rejected'){
                    $arr['auction_status']='تم رفض السلعة من قبل الادارة';
                    $arr['status_text']='تم رفض السلعة من قبل الادارة';
                }elseif ($obj->status=='accepted'){
                    $arr['auction_status']='بانتظار تسليم المركبة لساحة الحفظ';
                    $arr['status_text']='بانتظار تسليم المركبة لساحة الحفظ';
                }elseif ($obj->status=='delivered'){
                    $arr['auction_status']='تم استلام المركبة من قبل الادارة';
                    $arr['status_text']='تم استلام المركبة من قبل الادارة';
                }else{
                    $arr['auction_status']='تم جدولتها للمزاد';
                    $arr['status_text']='تم جدولتها للمزاد';
                }
                $arr['negotiation']=false;
                $arr['direct_pay']=false;
                $arr['user_price']="";
                $arr['live']=false;
                $arr['auction_type']= $obj->auction_type->name[$this->lang()];
                $arr['start_date']= 123;
                $arr['auction_duration']=1;
                $arr['auction_price']=0;
            }
            $arr['id']=(int)$obj->id;
            $year = $obj->year;
            $mark = $obj->mark->name[$this->lang()];
            $model= $obj->model->name[$this->lang()];
            if ($this->lang()=='ar'){
                $name= sprintf(' %d - %s - %s ' ,$year, $mark , $model );
            }else{
                $name= sprintf(' %s - %s - %s ', $year,  $mark , $model );
            }
            $arr['name']=$name;
            $arr['item_status']= $obj->item_status->name[$this->lang()];
            $arr['city']= $obj->city->name[$this->lang()];
            $arr['image']=$obj->images[0];
            $arr['images']=$obj->images;
            $arr['is_favourite']=$is_favourite;
            $arr['win']=$win;
            $arr["my_item"]=$my_item;
            $data[]=$arr;
        }
        return $data;
    }
}
