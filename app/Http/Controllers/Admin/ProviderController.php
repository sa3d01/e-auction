<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\userType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderController extends MasterController
{
    public function __construct(User $model)
    {
        $this->model = $model;
        $this->route = 'provider';
        parent::__construct();
    }
    public function validation_func($method, $id = null)
    {
        if ($method == 1)
            return ['name' => 'required', 'mobile' => 'required|unique:users|max:10|regex:/(05)[0-9]{8}/', 'email' => 'required|unique:users|email|max:50', 'image' => 'mimes:png,jpg,jpeg', 'password' => 'required|min:6'];
        return ['name' => 'required', 'mobile' => 'required|regex:/(05)[0-9]{8}/|max:10|unique:users,mobile,' . $id, 'email' => 'required|email|max:50|unique:users,email,' . $id, 'image' => 'mimes:png,jpg,jpeg'];
    }
    public function validation_msg()
    {
        return array(
            'unique' => ' مسجل بالفعل :attribute هذا الـ',
            'required' => ':attribute يجب ادخال الـ',
            'max' =>' يجب أﻻ تزيد قيمته عن :max عناصر :attribute',
            'min' =>' يجب أﻻ تقل قيمته عن :min عناصر :attribute',
            'email'=>'يرجى التأكد من صحة البريد الالكترونى',
            'regex'=>'تأكد من أن رقم الجوال يبدأ ب05 , ويحتوى على عشرة أرقام',
            'image.mimes' => 'يوجد مشكلة بالصورة',
        );
    }
    public function index()
    {
        $rows = $this->model->where('user_type_id',3)->orWhere('user_type_id',4)->get();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'provider',
            'title'=>'قائمة مزودى الخدمات',
            'index_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', ' الجوال' => 'mobile',' الرصيد' => 'wallet','تاريخ الانضمام'=>'created_at'],
            'selects'=>[
                [
                    'name'=>'user_type',
                    'title'=>'النوع'
                ],
            ],
            'status'=>true,
            'image'=>true,
        ]);
    }
    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'provider',
            'action'=>'admin.provider.store',
            'title'=>'أضافة مزود خدمة',
            'create_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'mobile', 'نص تعريفى' => 'note'],
            'status'=>true,
            'password'=>true,
            'image'=>true,
            'address'=>true,
            'selects'=>[
                [
                    'input_name'=>'user_type_id',
                    'rows'=>userType::where('id',3)->orWhere('id',4)->get(),
                    'title'=>'النوع'
                ],
            ],
        ]);
    }
    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $this->model->create($data);
        return redirect()->route('admin.user.index')->with('created');
    }
    public function show($id)
    {
        $row = User::findOrFail($id);
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'provider',
            'action'=>'admin.provider.update',
            'title'=>'الملف الشخصى',
            'edit_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'mobile', 'النص التعريفى' => 'note', 'الرصيد' => 'wallet'],
            'only_show'=>true,
            'status'=>true,
            'image'=>true,
            'address'=>true,
            'attachments'=>true
        ]);
    }
    public function activate($id,Request $request){
        $user=$this->model->find($id);
        if($user->status === 1){
            $user->update(
                [
                    'status'=>0,
                ]
            );
        }else{
            $user->update(
                [
                    'status'=>1,
                ]
            );
        }
        $user->refresh();
        return redirect()->back()->with('updated');
    }
}
