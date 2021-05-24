<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;

class UserController extends MasterController
{
    public function __construct(User $model)
    {
        $this->model = $model;
        $this->route = 'user';
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
        $rows = $this->model->latest()->get();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'user',
            'title'=>'قائمة العملاء',
            'index_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', ' الجوال' => 'phone','تاريخ الانضمام'=>'created_at'],
            'status'=>true,
            'image'=>true,
        ]);
    }
    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'user',
            'action'=>'admin.user.store',
            'title'=>'أضافة عميل',
            'create_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'mobile'],
            'status'=>true,
            'password'=>true,
            'image'=>true,
        ]);
    }
    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $data['user_type_id']=1;
        $this->model->create($data);
        return redirect()->route('admin.user.index')->with('created');
    }
    public function show($id)
    {
        $row = User::findOrFail($id);
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'user',
            'action'=>'admin.user.update',
            'title'=>'الملف الشخصى',
            'edit_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'mobile', 'المحفظة' => 'wallet', 'القوة الشرائية' => 'purchasing_power'],
            'selects'=>[[
                'title'=>'الباقة',
                'name'=>'package',
                'input_name'=>'package_id',
            ]],
            'status'=>true,
            'password'=>true,
            'image'=>true,
            'licence_image'=>true,
            'only_show'=>true,
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
