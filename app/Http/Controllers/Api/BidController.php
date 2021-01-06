<?php

namespace App\Http\Controllers\Api;

use App\Item;
use App\Setting;
use Illuminate\Http\Request;

class BidController extends MasterController
{
    protected $model;

    public function __construct(Item $model)
    {
        $this->model = $model;
        $this->purchasing_power_ratio=Setting::first()->value('purchasing_power_ratio');
        parent::__construct();
    }
    public function bid($item_id,Request $request){
        $user=$request->user();
        $item=Item::find($item_id);
        return $this->validate_purchasing_power($item,$user);
    }

    protected function validate_purchasing_power($item,$user){
        $user_purchasing_power=$user->purchasing_power;
        if ($user->package){
            $user_purchasing_power=$user_purchasing_power+$user->package->purchasing_power_increase;
        }
        $purchasing_power_admin_percent=$this->purchasing_power_ratio;

        

        return true;
    }
}
