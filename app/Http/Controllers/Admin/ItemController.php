<?php

namespace App\Http\Controllers\Admin;

use App\Item;

class ItemController extends MasterController
{
    public function __construct(Item $model)
    {
        $this->model = $model;
        $this->route = 'item';
        parent::__construct();
    }

    public function items($status)
    {
        $rows=$this->model->where('status',$status)->latest()->get();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'item',
            'title'=>'قائمة السلع',
            'index_fields'=>['الرقم التسلسلى' => 'id','تاريخ الطلب'=>'created_at'],
            'selects'=>[
                [
                    'name'=>'user',
                    'title'=>'المستخدم'
                ]
            ],
            'only_show'=>true,
            'status'=>true,
        ]);
    }

    public function show($id)
    {
        $row=$this->model->findOrFail($id);
        $fields=[
            'الرقم التسلسلى' => 'id',
            'تاريخ الطلب'=>'created_at',
            'تفاصيل الطلب'=>'note',
        ];
        if ($row->status!='new' || $row->price!=0){
            $fields['السعر']='price';
        }
        if ($row->cancel_reason!=null){
            $fields['سبب الالغاء']='cancel_reason';
        }
        if ($row->status!='new'){
            $fields['تم الدفع']='paid';
        }
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'item',
            'action'=>'admin.item.update',
            'title'=>'بيانات الطلب',
            'edit_fields'=>$fields,
            'selects'=>[
                [
                    'name'=>'user',
                    'title'=>'المستخدم',
                    'route'=>route('admin.user.show',[$row->user_id])
                ],
                [
                    'name'=>'provider',
                    'title'=>'مقدم الخدمة',
                    'route'=>route('admin.provider.show',[$row->provider_id])
                ],
                [
                    'name'=>'type',
                    'title'=>'نوع الخدمة'
                ],
            ],
            'only_show'=>true,
            'rate'=>true,
            'paid'=>true
        ]);
    }
}
