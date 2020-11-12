<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatCollection extends ResourceCollection
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
            $arr['id']=(int)$obj->id??0;
            $arr['sender']=[
                'id'=>$obj->sender_id,
                'name'=>$obj->sender->name,
                'image'=>$obj->sender->image,
            ];
            $arr['type']=$obj->type??"";
            if ($obj->type=='text'){
                $arr['msg']=$obj->msg??"";
            }else{
                $arr['msg']=asset('media/files/chat/').'/'.$obj->msg;
            }
            $arr['time']=$obj->published_from();
            $data[]=$arr;
        }
        return $data;
    }
}
