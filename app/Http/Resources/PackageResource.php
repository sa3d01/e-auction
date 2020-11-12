<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $arr['id']=(int) $this->id;
        $arr['image']=$this->image;
        if (\request()->header('lang')=='en'){
            $arr['name']=$this->name['en'];
            $arr['note']=$this->note['en'];
        }else{
            $arr['name']=$this->name['ar'];
            $arr['note']=$this->note['ar'];
        }
        $arr['price']=(int)$this->price;
        $arr['period']=(int)$this->period;
        return $arr;
    }
}
