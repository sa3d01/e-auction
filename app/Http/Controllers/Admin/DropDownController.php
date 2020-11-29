<?php

namespace App\Http\Controllers\Admin;

use App\Ask;
use App\DropDown;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DropDownController extends MasterController
{
    public function __construct(DropDown $model)
    {
        $this->model = $model;
        $this->route = 'drop_down';
//        $this->middleware('permission:view-car-options', ['only' => ['index']]);
//        $this->middleware('permission:edit-car-options', ['only' => ['update','activate']]);
//        $this->middleware('permission:create-car-options', ['only' => ['create']]);
        parent::__construct();
    }

    public function validation_func($method, $id = null)
    {
        return [
            'name_ar' => 'required',
            'name_en' => 'required',
        ];
    }

    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
        );
    }

    public function list($class)
    {
        $rows = $this->model->where('class',$class)->get();
        return View('dashboard.drop_down.index', [
                'rows' => $rows,
                'type'=>$class,
                'title'=>'قائمة البيانات',
                'index_fields'=>['الاسم' => 'name'],
                'languages'=>true,
                'status'=>true,
                'image'=> $class=="Mark" ? true : false,
            ]
        );
    }

    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'model',
            'action'=>'admin.model.store',
            'title'=>'أضافة ماركة',
            'create_fields'=>['الإسم' => 'name'],
            'languages'=>true,
            'select'=>[
                'name'=>'الماركة',
                'class'=>'Mark',
                'input_name'=>'parent_id',
                ],
            ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $name['ar']=$request['name_ar'];
        $data['name']=$name;
        $data['class']='Model';
        $this->model->create($data);
        return redirect()->route('admin.model.index')->with('created');
    }

    public function update($id,Request $request)
    {
        $this->validate($request, $this->validation_func(2),$this->validation_msg());
        $data=$request->all();
        $name['ar']=$request['name_ar'];
        $data['name']=$name;
        $this->model->find($id)->update($data);
        return redirect('admin/' . $this->route . '')->with('updated', 'تم التعديل بنجاح');
    }

    public function show($id)
    {
        $row = DropDown::findOrFail($id);
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'model',
            'action'=>'admin.model.update',
            'title'=>'موديل',
            'edit_fields'=>['الإسم' => 'name','الماركة' => 'parent_id'],
            'languages'=>true,
            'status'=>true
        ]);
    }

    public function activate($id){
        $row=$this->model->find($id);
        if($row->status==1){
            $history[date('Y-m-d')]['block']=[
                'time'=>date('H:i:s'),
                'admin_id'=>Auth::user()->id,
            ];
            $row->update(
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
            $row->update(
                [
                    'status'=>1,
                    'more_details'=>[
                        'history'=>$history,
                    ],
                ]
            );
        }
        $row->refresh();
        $row->refresh();
        return redirect()->back()->with('updated');
    }
}
