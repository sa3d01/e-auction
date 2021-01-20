<?php

namespace App\Http\Controllers\Api;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'title' => 'required',
            'message' => 'required',
        ];
    }
    public function validation_messages()
    {
        return array(
            'required' => ':attribute يجب ادخال الـ',
        );
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),$this->validation_rules(1),$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data=$request->all();
        $data['user_id']= auth()->user()->id;
        $this->model->create($data);
        return $this->sendResponse('تم الارسال بنجاح');
    }

}
