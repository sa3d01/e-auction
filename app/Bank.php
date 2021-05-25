<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use ModelBaseFunctions;

    protected $fillable = ['name', 'iban_number', 'status'];
    protected $casts = [
        'name' => 'json',
    ];
}
