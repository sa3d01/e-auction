<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use ModelBaseFunctions;

    private $route='transfer';
    private $images_link='media/images/transfer/';
//'item','item','sale','package','purchasing_power','other'
    protected $fillable = ['purchasing_type','money','user_id','type','more_details'];
    protected $casts = [
        'more_details' => 'json',
    ];
}
