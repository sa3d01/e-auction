<?php

namespace App\Http\Resources;

use App\Auction;
use App\Favourite;
use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Object_;
use tests\Mockery\Adapter\Phpunit\EmptyTestCase;

class ItemResource extends JsonResource
{
    function lang(){
        if (\request()->header('lang')){
            return \request()->header('lang');
        }else{
            return 'ar';
        }
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $auction=Auction::where('items', 'like', '%'.$this->id.'%')->first();
        $favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$this->id])->first();
        if ($favourite){
            $is_favourite=true;
        }else{
            $is_favourite=false;
        }
        return [
            'id'=> (int) $this->id,
            'images'=> $this->images,
            'start_date'=> $auction->start_date,
            'item_status'=> $this->item_status->name[$this->lang()],
            'auction_price'=> $this->auction_price,
            'name'=> $this->name,
            'city'=> $this->city->name[$this->lang()],
            'mark'=> $this->mark->name[$this->lang()],
            'model'=> $this->model->name[$this->lang()],
            'fetes'=> $this->fetes->name[$this->lang()],
            'kms_count'=> $this->kms_count,
            'color'=> $this->color->name[$this->lang()],
            'sunder_count'=> $this->sunder_count,
            'auction_type'=> $this->auction_type->name[$this->lang()],
            'is_favourite'=> $is_favourite,
        ];
    }
}
