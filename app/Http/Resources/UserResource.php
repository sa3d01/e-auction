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
            'name'=> $this->name??"",
            'phone'=> $this->phone??"",
            'email'=> $this->email??"",
            'image'=> $this->image ??"",
            'licence_image'=> $this->licence_image ?? '',
            'activation_code'=> $this->activation_code ? (int)$this->activation_code : '',
            'package'=> $this->package?new PackageResource($this->package) : new Object_(),
            'purchasing_power'=> (double)$this->purchasing_power ?? 0,
            'wallet'=> (double)$this->wallet ?? 0,
            'credit'=> (double)$this->credit ?? 0,
        ];
    }
}
