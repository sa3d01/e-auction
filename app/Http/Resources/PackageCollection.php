<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PackageCollection extends ResourceCollection
{
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
            $arr['id']=(int)$obj->id;
            $arr['image']=$obj->image;
            if (\request()->header('lang')=='en'){
                $arr['name']=$obj->name['en'];
                $arr['note']=$obj->note['en'];
            }else{
                $arr['name']=$obj->name['ar'];
                $arr['note']=$obj->note['ar'];
            }
            $arr['price']=(int)$obj->price;
            $arr['period']=(int)$obj->period;
            $data[]=$arr;
        }
        return $data;
    }
}
