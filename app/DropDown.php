<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class DropDown extends Model
{
    use ModelBaseFunctions;

    private $route='drop_down';
    private $images_link='media/images/drop_down/';

    protected $fillable = ['status','class','name','parent_id','image','more_details'];
    protected $casts = [
        'more_details' => 'json',
        'name' => 'json',
    ];
    public function nameForSelect(){
        return $this->name['ar'];
    }
    public function parent(){
        return $this->belongsTo(DropDown::class,'parent_id','id');
    }
}
