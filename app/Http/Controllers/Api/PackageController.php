<?php

namespace App\Http\Controllers\Api;

use App\Contact;
use App\DropDown;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackageCollection;
use App\Http\Resources\PackageResource;
use App\Http\Resources\UserResource;
use App\Package;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class PackageController extends MasterController
{
    protected $model;

    public function __construct(Package $model)
    {
        $this->model = $model;
        parent::__construct();
    }
    public function index(){
        return $this->sendResponse(new PackageCollection($this->model->all()));
    }
    public function show($id){

        return $this->sendResponse(new PackageResource($this->model->find($id)));
    }

}
