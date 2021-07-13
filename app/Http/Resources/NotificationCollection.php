<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
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
            $arr['type']=$obj->type;
            $arr['read']=($obj->read == 'true') ? true : false;
            if (\request()->header('lang')=='en'){
                try {
                    $arr['title']=$obj->note['en'];
                    $arr['note']=$obj->note['en'];
                }catch (\Exception $e){
                    $arr['title']=$obj->note['ar'];
                    $arr['note']=$obj->note['ar'];
                }
            }else{
                $arr['title']=$obj->note['ar'];
                $arr['note']=$obj->note['ar'];
            }
            $arr['item_id']=(int)$obj->item_id;
            $arr['published_from']=$obj->published_from();
            if ($obj->more_details != null){
                if (array_key_exists('offer_id',$obj->more_details)){
                    $arr['offer_id']=(int)$obj->more_details['offer_id'];
                }else{
                    unset($arr['offer_id']);
                }
                if (array_key_exists('win',$obj->more_details)){
                    $arr['win']=(bool)$obj->more_details['win'];
                }else{
                    unset($arr['win']);
                }
            }else{
                unset($arr['offer_id']);
                unset($arr['win']);
            }
            $data[]=$arr;
        }
        return $data;
    }
}
