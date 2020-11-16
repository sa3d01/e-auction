<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Object_;
use tests\Mockery\Adapter\Phpunit\EmptyTestCase;

class ItemResource extends JsonResource
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
            'user'=> new SmallUserResource($this->user),
            'paper_image'=>$this->paper_image,
            'mark'=> new DropDownResource($this->mark),
            'model'=> new DropDownResource($this->model),
            'item_status'=> new DropDownResource($this->item_status),
            'city'=> new DropDownResource($this->city),
            'sale_type'=> $this->sale_type->name['ar'],
            'images'=> $this->images,
            'model_class'=> $this->model_class,
            'factory'=> $this->factory,
            'kms'=> $this->kms,
        ];
    }
}
