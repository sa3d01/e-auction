<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use ModelBaseFunctions;

    private $route='setting';
    private $images_link='media/images/setting/';

    protected $fillable = ['about','licence','socials','contacts','purchasing_power_text',
        'purchasing_power_ratio','finish_papers','owner_tax_ratio','auction_increasing_period','app_ratio','add_item_tax','tax_ratio','more_details'];
    protected $casts = [
        'more_details' => 'json',
        'purchasing_power_text' => 'json',
        'contacts' => 'json',
        'socials' => 'json',
        'about' => 'json',
        'licence' => 'json',
    ];
}
