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

    public function activate()
    {
        if ($this->attributes['status'] == "approved"){
            return "<a class='block btn btn-success btn-sm' data-href='#' href='#'><i class='os-icon os-icon-check-circle'></i><span>رأى مفعل  </span></a>";
        }else{
            $action = route('admin.feed_back.activate', [$this->attributes['id']]);
            return "<a class='block btn btn-info btn-sm' data-href='$action' href='$action'><i class='os-icon os-icon-activity'></i><span>قبول !</span></a>";
        }
    }
}
