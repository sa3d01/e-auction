<?php

namespace App\Http\Controllers\Api;

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
        $data['about']=$setting->about['ar'];
        $data['licence']=$setting->licence['ar'];
        $data['private']=$setting->private['ar'];
        return $this->sendResponse($data);
    }

}
