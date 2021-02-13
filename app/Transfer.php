<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use ModelBaseFunctions;

    private $route='transfer';
    private $images_link='media/images/transfer/';
    protected $fillable = ['purchasing_type','money','user_id','type','status','more_details'];
    protected $casts = [
        'more_details' => 'json',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
