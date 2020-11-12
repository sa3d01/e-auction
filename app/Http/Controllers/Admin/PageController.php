<?php

namespace App\Http\Controllers\Admin;

use App\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends MasterController
{
    public function __construct(Page $model)
    {
        $this->model = $model;
        $this->route = 'page';
        parent::__construct();
    }
    public function validation_func($method, $id = null)
    {
        return ['title' => 'required','note'=>'required', 'image' => 'mimes:png,jpg,jpeg'];
    }
    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
            'image.mimes' => 'يوجد مشكلة بالصورة',
        );
    }
    public function index()
    {
        $rows = $this->model->all();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'page',
            'title'=>'قائمة الصفحات',
            'index_fields'=>['العنوان' => 'title'],
            'status'=>true,
            'image'=>true,
        ]);
    }
    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'page',
            'action'=>'admin.page.store',
            'title'=>'أضافة صفحة',
            'create_fields'=>['العنوان' => 'title', 'النص' => 'note'],
            'status'=>true,
            'image'=>true,
            'languages'=>true,
        ]);
    }
    public function store(Request $request)
    {
        return $request->all();
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $this->model->create($request->all());
        return redirect()->route('admin.page.index')->with('created');
    }
    public function show($id)
    {
        $row = $this->model->findOrFail($id);
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'page',
            'action'=>'admin.page.update',
            'title'=>'تفاصيل',
            'edit_fields'=>['العنوان' => 'title', 'النص' => 'note'],
            'status'=>true,
            'languages'=>true,
            'image'=>true,
        ]);
    }
    public function activate($id,Request $request){
        $page=$this->model->findOrFail($id);
        $history=$page->more_details['history'];
        if($page->status === 1){
            $history[date('Y-m-d')]['block']=[
                'time'=>date('H:i:s'),
                'admin_id'=>Auth::user()->id,
                'reason'=>$request['block_reason'],
            ];
            $page->update(
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
            $page->update(
                [
                    'status'=>1,
                    'more_details'=>[
                        'history'=>$history,
                    ],
                ]
            );
        }
        return redirect()->back()->with('updated');
    }
}
