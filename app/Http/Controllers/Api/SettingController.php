<?php

namespace App\Http\Controllers\Api;

use App\Ask;
use App\DropDown;
use App\Http\Controllers\Controller;
use App\Http\Resources\DropDownCollection;
use App\Http\Resources\UserResource;
use App\AuctionType;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class SettingController extends MasterController
{
    protected $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(){
        $setting=Setting::first();
        $data=[];
        if (\request()->header('lang')=='en'){
            $data['about']=$setting->about['en'];
            $data['licence']=$setting->licence['en'];
            $data['purchasing_power_text']=$setting->purchasing_power_text['en'];
        }else{
            $data['about']=$setting->about['ar'];
            $data['licence']=$setting->licence['ar'];
            $data['purchasing_power_text']=$setting->purchasing_power_text['ar'];
        }
        $data['app_ratio']=$setting->app_ratio;
        $data['tax_ratio']=$setting->tax_ratio;
        $data['add_item_tax']=$setting->add_item_tax;
        $data['socials']=$setting->socials;
        $data['purchasing_power_ration']=$setting->purchasing_power_ration;

        $data['less_tenThousand']=$setting->more_details['less_tenThousand'];
        $data['less_hundredThousand']=$setting->more_details['less_hundredThousand'];
        $data['more_hundredThousand']=$setting->more_details['more_hundredThousand'];

        return $this->sendResponse($data);
    }

    public function asks(){
        $asks=Ask::all();
        $data=[];
        foreach ($asks as $ask){
            if (\request()->header('lang')=='en'){
                $arr['ask']=$ask->ask['en'];
                $arr['answer']=$ask->answer['en'];
            }else{
                $arr['ask']=$ask->ask['ar'];
                $arr['answer']=$ask->answer['ar'];
            }
            $data[]=$arr;
        }
        return $this->sendResponse($data);
    }


}
