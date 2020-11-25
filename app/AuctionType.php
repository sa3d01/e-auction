<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class AuctionType extends Model
{
    use ModelBaseFunctions;

    private $route='sale_type';
    private $images_link='media/images/sale_type/';
    protected $fillable = ['name','more_details'];
    protected $casts = [
        'name' => 'json',
        'more_details' => 'json',
    ];
}
