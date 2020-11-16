<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    use ModelBaseFunctions;

    private $route='feed_back';
    private $images_link='media/images/feed_back/';

    protected $fillable = ['user_id','feed_back','status'];


    public function User(){
        return $this->belongsTo(User::class);
    }
}
