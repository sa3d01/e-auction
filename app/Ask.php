<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Ask extends Model
{
    use ModelBaseFunctions;

    private $route='ask';
    private $images_link='media/images/ask/';

    protected $fillable = ['ask','answer','order_by'];
    protected $casts = [
        'ask' => 'json',
        'answer' => 'json',
    ];


}
