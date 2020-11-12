<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use ModelBaseFunctions;
    private $route='package';
    private $images_link='media/images/package/';

    protected $fillable = ['image','name','note','price','period','purchasing_power_increase','paid_files_count'];
    protected $casts = [
        'name' => 'json',
        'note' => 'json',
    ];
}
