<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleController extends MasterController
{
    public function __construct(Role $model)
    {
        $this->model = $model;
        $this->route = 'role';
        parent::__construct();
    }

    public function validation_func($method, $id = null)
    {
        return [

        ];
    }

    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
        );
    }
    public function index()
    {
        $rows=Role::all();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'role',
            'title'=>'قائمة الأدوار',
            'index_fields'=>['الإسم'=>'blank','عدد الأعضاء'=>'users_count'],
        ]);
    }

    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'role',
            'action'=>'admin.role.store',
            'title'=>'أضافة دور',
            'permissions'=>true,
            'create_fields'=>[
                'الإسم' => 'blank',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $role=Role::firstOrCreate(
            [
                'name' => $request['blank'],
                'blank' => $request['blank'],
            ]
        );
        $role->syncPermissions($request['permissions']);
        return redirect()->route('admin.role.index')->with('created');
    }

    public function show($id)
    {
        return View('dashboard.show.show', [
            'row' => $this->model->findOrFail($id),
            'type'=>'role',
            'action'=>'admin.role.update',
            'title'=>'بيانات الدور',
            'edit_fields'=>[
                'الإسم' => 'blank'
            ],
            'permissions'=>true,
        ]);
    }
    public function activate($id,Request $request){
        $sale=$this->model->find($id);
        $history=$sale->more_details['history'];
        if($sale->status==1){
            $history[date('Y-m-d')]['block']=[
                'time'=>date('H:i:s'),
                'admin_id'=>Auth::user()->id,
            ];
            $sale->update(
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
            $sale->update(
                [
                    'status'=>1,
                    'more_details'=>[
                        'history'=>$history,
                    ],
                ]
            );
        }
        $sale->refresh();
        return redirect()->back()->with('updated');
    }
    public function update($id, Request $request)
    {
        $role=Role::findById($id);
        $role->update(
            [
                'name' => $request['blank'],
                'blank' => $request['blank'],
            ]
        );
        $role->syncPermissions($request['permissions']);
        return redirect()->back()->with('updated', 'تم التعديل بنجاح');
    }
}
