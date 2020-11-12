<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\userType;
use App\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends MasterController
{
    public function __construct(User $model)
    {
        $this->model = $model;
        $this->route = 'wallet';
        parent::__construct();
    }

    public function index()
    {
        $rows = $this->model->where('wallet','!=',0)->get();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'wallet',
            'title'=>'التقارير المالية',
            'index_fields'=>['الاسم' => 'name', ' الجوال' => 'mobile',' الرصيد' => 'wallet'],
            'selects'=>[
                [
                    'name'=>'user_type',
                    'title'=>'النوع'
                ],
            ],
            'status'=>true,
            'image'=>true,
        ]);
    }
    public function create()
    {
        return View('dashboard.create.create', [
            'type'=>'provider',
            'action'=>'admin.provider.store',
            'title'=>'أضافة مزود خدمة',
            'create_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'mobile', 'نص تعريفى' => 'note'],
            'status'=>true,
            'password'=>true,
            'image'=>true,
            'address'=>true,
            'selects'=>[
                [
                    'input_name'=>'user_type_id',
                    'rows'=>userType::where('id',3)->orWhere('id',4)->get(),
                    'title'=>'النوع'
                ],
            ],
        ]);
    }
    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $data=$request->all();
        $this->model->create($data);
        return redirect()->route('admin.user.index')->with('created');
    }
    public function show($id)
    {
        $row = User::findOrFail($id);
        $provider_orders=$row->provider_orders->pluck('id');
        $rows=Wallet::whereIn('order_id',$provider_orders)->latest()->get();
        return View('dashboard.wallet.show', [
            'row' => $row,
            'type'=>'wallet',
            'title'=>'محفظة مزود خدمة',
            'show_fields'=>['الاسم' => 'name', 'البريد الإلكترونى' => 'email', 'الجوال' => 'mobile', 'النص التعريفى' => 'note', 'الرصيد' => 'wallet'],
            'index_fields'=>['الرقم التسلسلى' => 'id','تكلفة الطلب الاجمالية' => 'price', ' نسبة التطبيق' => 'app_ratio'],
            'rows'=>$rows,
            'image'=>true,
            'address'=>true,
            'attachments'=>true
        ]);
    }
    public function activate($id,Request $request){
        $user=$this->model->find($id);
        if($user->status == 1){
            $user->update(
                [
                    'status'=>0,
                ]
            );

        }else{
            $user->update(
                [
                    'status'=>1,
                ]
            );
        }
        $user->refresh();
        return redirect()->back()->with('updated');
    }
}
