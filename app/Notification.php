<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use ModelBaseFunctions;

    private $route='notification';

    protected $fillable = ['receiver_id','item_id','title','note','read','type','admin_notify_type','more_details'];
    protected $casts = [
        'title' => 'json',
        'note' => 'json',
        'more_details' => 'json',
    ];

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
