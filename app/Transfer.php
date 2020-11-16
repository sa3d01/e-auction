<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use ModelBaseFunctions;

    private $route='transfer';
    private $images_link='media/images/transfer/';
//'wallet','item','sale','package','purchasing_power','other'
    protected $fillable = ['money','user_id','type','more_details'];
    protected $casts = [
        'more_details' => 'json',
    ];
}
