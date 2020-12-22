<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use ModelBaseFunctions;

    private $route='report';
    private $images_link='media/images/report/';
    protected $fillable = ['title','note','images','price','item_id'];
    protected $casts = [
        'title' => 'json',
        'note' => 'json',
        'images' => 'array',
    ];
    public function nameForSelect(){
        return $this->title['ar'] ;
    }
    public function item(){
        return $this->belongsTo(Item::class);
    }
    public function imagesArray(){
        return $this->attributes['images'];
    }
}
