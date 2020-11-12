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
        return [
            'id'=> (int)$this->id,
            'type'=> $this->type,
            'read'=> ($this->read == 'true') ? true : false,
            'title'=> $this->title,
            'note'=> $this->note,
            'order_id'=>(int) $this->order_id,
            'published_from'=> $this->published_from()
        ];
    }
}
