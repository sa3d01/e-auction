<?php

namespace App\Http\Controllers\Api;

use App\Contact;
use App\DropDown;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class ContactController extends MasterController
{
    protected $model;

    public function __construct(Contact $model)
    {
        $this->model = $model;
        parent::__construct();
    }
    public function validation_rules($method, $id = null)
    {
        return [
            'type_id' => 'required',
            'message' => 'required',
        ];
    }
    public function validation_messages()
    {
        return array(
            'required' => ':attribute يجب ادخال الـ',
        );
    }
    public function types(){
        $types=DropDown::whereClass('Contact')->get();
        $data=[];
        foreach ($types as $type){
            $arr['id']=$type->id;
            $arr['name']=$type->name['ar'];
            $data[]=$arr;
        }
        return $this->sendResponse($data);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),$this->validation_rules(1),$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data=$request->all();
        $data['user_id']=auth()->user()->id;
        $this->model->create($data);
        return $this->sendResponse('تم الارسال بنجاح');
    }
}
