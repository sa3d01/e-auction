<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AdminController extends MasterController
{
    public function __construct(Admin $model)
    {
        $this->model = $model;
//        $this->middleware('permission:view-admins', ['only' => ['index']]);
//        $this->middleware('permission:add-admins', ['only' => ['create']]);
//        $this->middleware('permission:edit-admins', ['only' => ['show','activate']]);
        parent::__construct();
    }
    public function validation_func($method, $id = null)
    {
        if ($method == 1)
            return ['name' => 'required', 'email' => 'email|max:255|unique:admins', 'image' => 'mimes:png,jpg,jpeg', 'password' => 'required|min:6'];
        return ['name' => 'required', 'email' => 'email|max:255|unique:admins,email,' . $id, 'image' => 'mimes:png,jpg,jpeg'];
    }
    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
            'email.unique' => 'هذا البريد مسجل من مقبل',
            'image.mimes' => 'يوجد مشكلة بالصورة',
        );
    }
    public function index()
    {
        $rows = Admin::all();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'admin',
            'title'=>'قائمة الإدارة',
            'index_fields'=>['الاسم' => 'name','البريد الإلكترونى' => 'email','الدور ' => 'role'],
            'image'=>true,
            'status'=>true,
        ]);
    }
    public function show($id)
    {
        $row = Admin::find($id);
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'admin',
            'action'=>'admin.admin.update',
            'title'=>'الملف الشخصى',
            'edit_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email','الدور ' => 'role'],
            'status'=>true,
            'password'=>true,
            'image'=>true,
        ]);
    }
    public function profile()
    {
        $row = Auth::user();
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'admin',
            'action'=>'admin.update_profile',
            'title'=>'الملف الشخصى',
            'edit_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email'],
            'status'=>true,
            'password'=>true,
            'image'=>true,
        ]);
    }
    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'admin',
            'action'=>'admin.admin.store',
            'title'=>'أضافة مدير',
            'create_fields'=>['الاسم' => 'name','البريد الإلكترونى' => 'email','الدور ' => 'role'],
            'status'=>true,
            'image'=>true,
            'password'=>true,
        ]);
    }
    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $data['user_type_id']=2;
        $admin=$this->model->create($data);
        $role=Role::findById($request['role_id']);
        $admin->assignRole($role->name);
        return redirect()->route('admin.admin.index')->with('created');
    }
    public function update($id, Request $request)
    {
        $this->validate($request, $this->validation_func(2, $id),$this->validation_msg());
        $admin = $this->model->find($id);
        $data=$request->all();
        $admin->update($data);
        $new_role=Role::findById($request['role_id']);
        $admin->syncRoles($new_role);
        return redirect()->back()->with('updated', 'تم التعديل بنجاح');
    }
    public function update_profile($id, Request $request)
    {
        $this->validate($request, $this->validation_func(2, $id),$this->validation_msg());
        $admin = Auth::user();
        $data=$request->all();
        $admin->update($data);
        return redirect()->back()->with('updated', 'تم التعديل بنجاح');
    }
    public function activate($id,Request $request){
        $admin=$this->model->find($id);
        $history=$admin->more_details['history'];
        if($admin->status==1){
            $history[date('Y-m-d')]['block']=[
                'time'=>date('H:i:s'),
                'admin_id'=>Auth::user()->id,
                'reason'=>$request['block_reason'],
            ];
            $admin->update(
                [
                    'status'=>0,
                    'more_details'=>[
                        'history'=>$history,
                    ],
                ]
            );
        }else{
            $history[date('Y-m-d')]['approve']=[
                'time'=>date('H:i:s'),
                'admin_id'=>Auth::user()->id,
            ];
            $admin->update(
                [
                    'status'=>1,
                    'more_details'=>[
                        'history'=>$history,
                    ],
                ]
            );
        }
        $admin->refresh();
        return redirect()->back()->with('updated');
    }
}
