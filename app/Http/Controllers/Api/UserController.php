<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Package;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends MasterController
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
        parent::__construct();
    }
    public function validation_rules($method, $id = null)
    {
        if ($method == 2) {
            $rules['phone'] = 'nullable|regex:/(05)[0-9]{8}/|max:10|unique:users,phone,' . $id;
            $rules['email'] = 'nullable|email|max:50|unique:users,email,' . $id;
            $rules['name'] = 'nullable|max:30';
            $rules['device'] = 'required';
        } else {
            $rules = [
                'phone' => 'required|unique:users|max:10|regex:/(05)[0-9]{8}/',
                'email' => 'required|unique:users|email|max:50',
                'name' => 'required|max:30',
                'password' => 'required|min:8',
                'device' => 'required',
            ];
        }
        return $rules;
    }
    public function validation_messages()
    {
        return array(
            'unique' => ' مسجل بالفعل :attribute هذا الـ',
            'required' => ':attribute يجب ادخال الـ',
            'max' =>' يجب أﻻ تزيد قيمته عن :max عناصر :attribute',
            'min' =>' يجب أﻻ تقل قيمته عن :min عناصر :attribute',
            'email'=>'يرجى التأكد من صحة البريد الالكترونى',
            'regex'=>'تأكد من أن رقم الجوال صحيح ويبدا بـ 05 '
        );
    }

    function sendToPhone($phone,$activation_code){
        //todo remove 0 and add key country
        //Mail::to($email)->send(new ConfirmCode($activation_code));
    }
    function sendToMail($email,$activation_code){
//        Mail::to($email)->send(new ConfirmCode($activation_code));
    }
    function send_code($activation_code,$email=null,$phone=null){
        if ($email){
            $this->sendToMail($email,$activation_code);
        }
        if ($phone){
            $this->sendToPhone($phone,$activation_code);
        }
    }
    public function authPhoneAndMail(Request $request)
    {
        $validator = Validator::make($request->only('email','phone'),
            [
                'email'=>'nullable|email|max:50',
                'phone'=>'nullable|regex:/(05)[0-9]{8}/|max:10'
            ]
            ,$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        if (!$request->has('email') && !$request->has('phone')){
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        $activation_code =2021;// rand(1111, 9999);
        $this->send_code($activation_code,$request['email'],$request['phone']);
        $user = User::where('email',$request['email'])->orWhere('phone',$request['phone'])->first();
        $all = $request->all();
        $all['activation_code'] = $activation_code;
        if (!$user) {
            User::create($all);
        }else{
            $user->update($all);
        }
        return $this->sendResponse(['activation_code'=>$activation_code]);
    }
    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->only('email','activation_code'),
            [
                'activation_code'=>'required',
                'email'=>'nullable|email|max:50',
                'phone'=>'nullable|regex:/(05)[0-9]{8}/|max:10'
            ]
            ,$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        if (!$request->has('email') && !$request->has('phone')){
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        $user = User::where('email',$request['email'])->orWhere('phone',$request['phone'])->first();
        if (!$user) {
            return $this->sendError('المستخدم غير مسجل');
        }
        if ($user->activation_code===$request['activation_code']){
            $data= new UserResource($user);
            if ($request->has('email')){
                $user->update([
                    'activation_code'=>null,
                    'email_verified_at' => Carbon::now()
                ]);
            }else{
                $user->update([
                    'activation_code'=>null,
                    'phone_verified_at' => Carbon::now()
                ]);
            }
            $token = auth()->login($user);
            return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
        }else{
            return $this->sendError('كود التفعيل غير صحيح');
        }
    }
    public function update(Request $request){
        $user = auth()->user();
        $validator = Validator::make($request->all(),$this->validation_rules(2,$user->id),$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $user->update($request->except(['package_id','purchasing_power']));
        $data= new UserResource($user);
        $token = auth()->login($user);
        return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
    }












    function native_phone($user){
        return trim($user->phone,$user->phone_details['country_key']);
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(),$this->validation_rules(1),$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $activation_code = rand(1111, 9999);
        $this->send_code($activation_code,$request['email'],$request['phone']);
        $all = $request->all();
        $all['activation_code'] = $activation_code;
        $user = User::create($all);
        $token = auth()->login($user);
        $data= new UserResource($user);
        return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
    }
    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->only('email'),['email'=>'required|email|max:50'],$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $user = User::where('email',$request['email'])->first();
        if (!$user) {
            return $this->sendError('المستخدم غير مسجل');
        }
        $activation_code = rand(1111, 9999);
        $this->send_code($user->phone,$activation_code);
        $user->update(['activation_code'=>$activation_code]);
        return $this->sendResponse(['activation_code'=>$activation_code]);
    }

    public function forgetPassword(Request $request){
        $validator = Validator::make(
            $request->only('password'),
            [
                'password' => 'required|min:8',
            ],
            $this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $user=auth()->user();
        if ($user){
            $user->update(['password'=>$request['password']]);
            $token = auth()->login($user);
            $data= new UserResource($user);
            return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
        }else{
            return $this->sendError('يوجد مشكلة بالبيانات');
        }
    }
    public function login(Request $request){
        $validator = Validator::make($request->only('phone_details','phone','password','device'),['device'=>'required','phone' => 'required|max:10|regex:/[0-9]{10}/'],$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $cred=$request->only(['phone','password']);
        $token=auth()->attempt($cred);
        if ($token){
            $user=auth()->user();
            $user->update([
                'device'=>[
                    'id'=>$request->device['id'],
                    'type'=>$request->device['type'],
                ]
            ]);
            $data= new UserResource($user);
            return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
        }else{
            return $this->sendError('يوجد مشكلة بالبيانات');
        }
    }
    public function logout(Request $request){
        $user=auth()->user();
        $user->update([
            'device'=>[
                'id'=>null,
                'type'=>null,
            ]
        ]);
        auth()->logout();
        return $this->sendResponse('');
    }
    public function updatePassword(Request $request){
        $validator = Validator::make(
            $request->only('password','old_password'),
            [
                'old_password' => 'required',
                'password' => 'required|min:8',
            ],
            $this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $user = auth()->user();
        $token=auth()->attempt(['phone'=>$this->native_phone($user),'password'=>$request['old_password']]);
        if ($token){
            $user->update(['password'=>$request['password']]);
            $data= new UserResource($user);
            return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
        }else{
            return $this->sendError('كلمة المرور القديمة غير صحيحة');
        }
     }
    public function profile(){
        $user = auth()->user();
        $token = auth()->login($user);
        $data= new UserResource($user);
        return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
    }
    public function show($id){
        $user = User::find($id);
        $data= new UserResource($user);
        return $this->sendResponse($data);
    }
}
