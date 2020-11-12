<?php

namespace App\Http\Controllers\Api;

use App\DropDown;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
        $data['socials']=$setting->socials;
        return $this->sendResponse($data);
    }
    public function partners(){
        $partners=DropDown::whereClass('Partner')->get();
        $data=[];
        foreach ($partners as $partner){
            if (\request()->header('lang')=='en'){
                $arr['name']=$partner->name['en'];
            }else{
                $arr['name']=$partner->name['ar'];
            }
            $arr['image']=$partner->image;
            $data[]=$arr;
        }
        return $this->sendResponse($data);
    }

}
