<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
        $arr['sender']=[
            'id'=>$this->sender->id,
            'name'=>$this->sender->name,
            'image'=>$this->sender->image,
        ];
        $arr['type']=$this->type;
        if ($this->type=='text'){
            $arr['msg']=$this->msg??"";
        }else{
            $arr['msg']=asset('media/files/chat/').'/'.$this->msg;
        }
        $arr['time']=$this->published_from();
        return $arr;
    }
}
