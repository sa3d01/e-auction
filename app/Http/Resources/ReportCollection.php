<?php

namespace App\Http\Resources;

use App\Auction;
use App\Favourite;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportCollection extends ResourceCollection
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
            $arr['id']=(int)$obj->id;
            $arr['title']= $obj->title[$this->lang()];
            $arr['note']= $obj->note[$this->lang()];
            $images=[];
            foreach ($obj->images as $image){
                $images[]=asset('media/images/report/'.$image);
            }
            $arr['images']=$images;
            $arr['price']=$obj->price;
            $data[]=$arr;
        }
        return $data;
    }
}
