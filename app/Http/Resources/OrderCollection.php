<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
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
            $arr['user']=[
                'id'=>$obj->user_id,
                'name'=>$obj->user->name,
                'image'=>$obj->user->image,
            ];
            $arr['provider']=[
                'id'=>$obj->provider_id,
                'name'=>$obj->provider->name,
                'image'=>$obj->provider->image,
            ];
            $arr['type']=new OrderTypeResource($obj->type);
            $arr['paid']=$obj->paid==0?false:true;
            $arr['cancel_reason']=$obj->cancel_reason;
            $data[]=$arr;
        }
        return $data;
    }
}
