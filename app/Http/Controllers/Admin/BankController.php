<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\DropDown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankController extends MasterController
{
    public function __construct(Bank $model)
    {
        $this->model = $model;
        $this->route = 'bank';
        parent::__construct();
    }

    public function validation_func($method, $id = null)
    {
        return [
            'name_ar' => 'required',
            'name_en' => 'required',
            'iban_number' => 'required',
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
        $rows = $this->model->all();
        $index_fields=['الاسم' => 'name','رقم الإيبان'=>'iban_number'];
        return View('dashboard.bank.index', [
                'rows' => $rows,
                'title'=>'الحسابات البنكية',
                'index_fields'=>$index_fields,
                'languages'=>true,
                'status'=>true,
            ]
        );
    }
    public function create(){
        return View('dashboard.bank.create', [
            'type'=>'bank',
            'action'=>'admin.bank.store',
            'title'=>'أضافة حساب بنكى',
            'create_fields'=>['الاسم'=>'name','iban'=>'iban_number'],
            'languages'=>true,
        ]);
    }
    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $name['ar']=$request['name_ar'];
        $name['en']=$request['name_en'];
        $data['name']=$name;
        $data['iban_number']=$request['iban_number'];
        $this->model->create($data);
        return redirect()->route('admin.bank.index')->with('created');
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
        $row = Bank::findOrFail($id);
        $edit_fields=['الاسم' => 'name','رقم الايبان'=>'iban_number'];
        return View('dashboard.bank.show', [
            'row' => $row,
            'action'=>'admin.bank.update',
            'title'=>'الحسابات البنكية',
            'edit_fields'=>$edit_fields,
            'languages'=>true,
            'status'=>true,
        ]);
    }

    public function activate($id){
        $row=$this->model->find($id);
        if($row->status==1){
            $row->update(
                [
                    'status'=>0,
                ]
            );
        }else{
            $row->update(
                [
                    'status'=>1,
                ]
            );
        }
        $row->refresh();
        $row->refresh();
        return redirect()->back()->with('updated');
    }
}
