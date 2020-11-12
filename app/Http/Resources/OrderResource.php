<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (count($this->ratings)){
            $rate=(double)$this->ratings->avg('rate');
        }else{
            $rate=0;
        }
        return [
            'id'=>(int)  $this->id,
            'status'=> $this->status,
            'user'=>[
                'id'=>$this->user_id,
                'name'=>$this->user->name,
                'image'=>$this->user->image,
            ],
            'provider'=>[
                'id'=>$this->provider_id,
                'name'=>$this->provider->name,
                'image'=>$this->provider->image,
                'rating'=>0,
            ],
            'created_at'=> $this->published_at(),
            'type'=> new OrderTypeResource($this->type),
            'note'=> $this->note,
            'paid'=>$this->paid==0?false:true,
            'price'=>(int)$this->price,
            'cancel_reason'=>$this->cancel_reason,
            'rating'=>$rate
        ];
    }
}
