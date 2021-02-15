<?php

namespace App\Http\Controllers\Admin;

use App\DropDown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DropDownController extends MasterController
{
    public function __construct(DropDown $model)
    {
        $this->model = $model;
        $this->route = 'drop_down';
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
        if ($class=="Mark"||$class=="Partner"){
            $image=true;
        }else{
            $image=false;
        }
        if ($class=="Model"){
            $index_fields=['الاسم' => 'name','الماركة'=>'parent_id'];
        }else{
            $index_fields=['الاسم' => 'name'];
        }
        return View('dashboard.drop_down.index', [
                'rows' => $rows,
                'type'=>$class,
                'title'=>'قائمة البيانات',
                'index_fields'=>$index_fields,
                'languages'=>true,
                'status'=>true,
                'image'=> $image,
            ]
        );
    }

    public function create($class=null)
    {
        $image=null;
        $select=null;
        if ($class=='Partner'){
            $image=true;
        }elseif ($class=='Mark'){
            $image=true;
        }elseif ($class=='Model'){
            $select=[
                'name'=>'الماركة',
                'class'=>'Mark',
                'input_name'=>'parent_id',
            ];
        }
        return View('dashboard.drop_down.create', [
            'type'=>$class,
            'action'=>'admin.drop_down.store',
            'title'=>'أضافة ',
            'create_fields'=>['الإسم' => 'name'],
            'languages'=>true,
            'image'=>$image,
            'selects'=>$select,
            ]
        );
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
        $name['en']=$request['name_en'];
        $data['name']=$name;
        $this->model->find($id)->update($data);
        return back()->with('updated', 'تم التعديل بنجاح');
    }

    public function show($id)
    {
        $row = DropDown::findOrFail($id);
        if ($row->class=="Mark"||$row->class=="Partner"){
            $image=true;
        }else{
            $image=false;
        }
        if ($row->class=="Model"){
            $edit_fields=['الاسم' => 'name','الماركة'=>'parent_id'];
        }else{
            $edit_fields=['الاسم' => 'name'];
        }
        return View('dashboard.drop_down.show', [
            'row' => $row,
            'type'=>$row->class,
            'action'=>'admin.drop_down.update',
            'title'=>$row->class,
            'edit_fields'=>$edit_fields,
            'languages'=>true,
            'status'=>true,
            'image'=>$image
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
