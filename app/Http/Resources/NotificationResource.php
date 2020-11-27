<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (\request()->header('lang')=='en'){
            $title=$this->title['en'];
            $note=$this->note['en'];
        }else{
            $title=$this->title['ar'];
            $note=$this->note['ar'];
        }
        return [
            'id'=> (int)$this->id,
            'type'=> $this->type,
            'read'=> ($this->read == 'true') ? true : false,
            'title'=> $title,
            'note'=> $note,
            'item_id'=>(int) $this->item_id,
            'published_from'=> $this->published_from()
        ];
    }
}
