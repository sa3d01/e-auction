<?php

namespace App\Http\Controllers\Api;

use App\Ask;
use App\DropDown;
use App\Http\Controllers\Controller;
use App\Http\Resources\DropDownCollection;
use App\Http\Resources\UserResource;
use App\SaleType;
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
        }else{
            $data['about']=$setting->about['ar'];
            $data['licence']=$setting->licence['ar'];
        }
        $data['add_item_tax']=$setting->add_item_tax;
        $data['socials']=$setting->socials;
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
