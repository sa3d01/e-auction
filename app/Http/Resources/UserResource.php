<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Object_;
use tests\Mockery\Adapter\Phpunit\EmptyTestCase;

class UserResource extends JsonResource
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
            'id'=> (int) $this->id,
            'name'=> $this->name,
            'phone_details'=> $this->phone_details ?? new Object_(),
            'phone'=> $this->phone,
            'email'=> $this->email,
            'image'=> $this->image ?? '',
            'activation_code'=> $this->activation_code ? (int)$this->activation_code : '',
            'location'=> $this->location ?? new Object_(),
            'online'=> (int)$this->online,
        ];
    }
}
