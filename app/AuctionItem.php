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
        $arr['user_price']="";
        $arr['status']=$this->more_details['status'];
        $end_auction=Carbon::createFromTimestamp($this->attributes['start_date'])->addSeconds($this->auction->duration);
        if ($this->more_details['status']!='paid' && $this->more_details['status']!='expired'){
            if ($this->item->auction_type_id==4){
                //البيع المباشر
                //اي حد مادام مانتهاش المزايده عليه ولو انتهت المفاوضه بتكون مع اعلى مزايد
                if ($end_auction < Carbon::now()){
                    $soon_winner=AuctionUser::where('item_id',$this->attributes['item_id'])->latest()->first();
                    if ($soon_winner){
                        $this->update([
                            'more_details'=>[
                                'status'=>'negotiation'
                            ]
                        ]);
                        if ($authed_user_id==$soon_winner->user_id){
                            $arr['negotiation']=true;
                            $arr['direct_pay']=true;
                        }
                    }
                }else{
                    $arr['negotiation']=true;
                    $arr['direct_pay']=true;
                }
                $arr['user_price']=$this->item->price;
            }elseif ($this->item->auction_type_id==3){
                //البيع لأقل سعر يقبل به البائع
                //مع اعلى مزايد بعد انتهاء المزاد لو المزاد موصلش للسعر المطلوب
                if ($end_auction < Carbon::now()){
                    $soon_winner=AuctionUser::where('item_id',$this->attributes['item_id'])->latest()->first();
                    if ($soon_winner && ($soon_winner->price < $this->item->price)){
                        $this->update([
                            'more_details'=>[
                                'status'=>'negotiation'
                            ]
                        ]);
                        if ($authed_user_id==$soon_winner->user_id){
                            $arr['negotiation']=true;
                        }
                    }
                }
            }elseif ($this->item->auction_type_id==2){
                //البيع تحت موافقة البائع
                //مع اعلى مزايد بعد انتهاء المزاد
                if ($end_auction < Carbon::now()){
                    $soon_winner=AuctionUser::where('item_id',$this->attributes['item_id'])->latest()->first();
                    if ($soon_winner){
                        $this->update([
                            'more_details'=>[
                                'status'=>'negotiation'
                            ]
                        ]);
                        if ($authed_user_id==$soon_winner->user_id){
                            $arr['negotiation']=true;
                        }
                    }
                }
            }
        }
        return $arr;
    }
}
