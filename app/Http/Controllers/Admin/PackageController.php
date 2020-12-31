<?php

namespace App\Http\Controllers\Admin;

use App\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends MasterController
{
    public function __construct(Package $model)
    {
        $this->model = $model;
        $this->route = 'package';
        parent::__construct();
    }
    public function validation_func($method, $id = null)
    {
        return [
            'name_ar' => 'required',
            'name_en' => 'required',
            'note_ar' => 'required',
            'note_en' => 'required',
            'image' => 'required|image|mimes:jpeg,jpg,png,jpg,gif,svg',
        ];
    }

    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
            'image' => 'الصورة المرفقة غير صالحة',
        );
    }
    public function create(){
        return View('dashboard.package.create', [
            'type'=>'package',
            'action'=>'admin.package.store',
            'title'=>'أضافة باقة',
            'create_fields'=>['السعر'=>'price','المدة (بالأشهر)'=>'period','قيمة زيادة القوة الشرائية'=>'purchasing_power_increase','عدد ملفات الفحص المدفوعة المتاحة'=>'paid_files_count'],
            'languages'=>true,
            'image'=>true,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $name['ar']=$request['name_ar'];
        $name['en']=$request['name_en'];
        $data['name']=$name;
        $note['ar']=preg_split("/\r\n|\n|\r/", $request['note_ar']);
        $note['en']=preg_split("/\r\n|\n|\r/", $request['note_en']);
        $data['note']=$note;
        $this->model->create($data);
        return redirect()->route('admin.package.index')->with('created');
    }
    public function index()
    {
        $rows=$this->model->latest()->get();
        return View('dashboard.package.index', [
            'rows' => $rows,
            'type'=>'package',
            'title'=>'قائمة الباقات',
            'index_fields'=>['الاسم'=>'name','التوصيف'=>'note'],
        ]);
    }

}
