<?php

namespace App\Http\Controllers\Admin;

use App\Notification;
use App\Order;
use App\User;
use App\UserSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends MasterController
{
    public function __construct(Order $model)
    {
        $this->model = $model;
        $this->route = 'order';

        parent::__construct();
    }

    public function orders($status)
    {
        $rows=$this->model->where('status',$status)->latest()->get();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'order',
            'title'=>'قائمة الطلبات',
            'index_fields'=>['الرقم التسلسلى' => 'id','تاريخ الطلب'=>'created_at'],
            'selects'=>[
                [
                    'name'=>'user',
                    'title'=>'المستخدم'
                ],
                [
                    'name'=>'provider',
                    'title'=>'مقدم الخدمة'
                ],
                [
                    'name'=>'type',
                    'title'=>'نوع الخدمة'
                ],
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
            'type'=>'order',
            'action'=>'admin.order.update',
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
