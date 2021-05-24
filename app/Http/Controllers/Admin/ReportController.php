<?php

namespace App\Http\Controllers\Admin;

use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends MasterController
{
    public function __construct(Report $model)
    {
        $this->model = $model;
        $this->route = 'report';
        parent::__construct();
    }
    public function validation_func()
    {
        return [
            'title_ar' => 'required',
            'title_en' => 'required',
            'note_ar' => 'required',
            'note_en' => 'required',
            'images' => 'required',
        ];
    }

    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
        );
    }
    public function add($item_id){
        return View('dashboard.report.create', [
            'type'=>'report',
            'item_id'=>$item_id,
            'action'=>'admin.report.store',
            'title'=>'أضافة تقرير فحص',
            'create_fields'=>['العنوان' => 'title','الوصف' => 'note','السعر'=>'price'],
            'languages'=>true,
            'images'=>true,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(),$this->validation_msg());
        $data=$request->all();
        $title['ar']=$request['title_ar'];
        $title['en']=$request['title_en'];
        $data['title']=$title;
        $note['ar']=$request['note_ar'];
        $note['en']=$request['note_en'];
        $data['note']=$note;
        if($request->images){
            foreach ($request->images as $file) {
                $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move('media/images/report', $filename);
                $images[]=$filename;
            }
            $data['images']=$images;
        }
        $this->model->create($data);
        return redirect()->route('admin.item.status',['accepted'])->with('created');
    }

    public function update($id, Request $request)
    {
        $this->validate($request, $this->validation_func(),$this->validation_msg());
        $data=$request->all();
        $title['ar']=$request['title_ar'];
        $title['en']=$request['title_en'];
        $data['title']=$title;
        $note['ar']=$request['note_ar'];
        $note['en']=$request['note_en'];
        $data['note']=$note;
        if($request->images){
            foreach ($request->images as $file) {
                $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move('media/images/report', $filename);
                $images[]=$filename;
            }
            $data['images']=$images;
        }
        $report=$this->model->find($id);
        $report->update($data);
        return redirect()->route('admin.item.status',['accepted'])->with('updated');
    }

}
