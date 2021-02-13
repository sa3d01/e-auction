<?php

namespace App\Http\Controllers\Admin;

use App\Ask;
use App\DropDown;
use App\Notification;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends MasterController
{
    public function __construct(Transfer $model)
    {
        $this->model = $model;
        $this->route = 'transfer';
        parent::__construct();
    }


    public function index()
    {
        $rows = $this->model->where('purchasing_type','bank')->latest()->get();
        return View('dashboard.transfer.index', [
            'rows' => $rows,
            'type'=>'transfer',
            'title'=>'قائمة الحوالات',
            'index_fields'=>['المستخدم' => 'user_id','المبلغ' => 'money','تاريخ الارسال' => 'created_at'],
            'status'=>true,
        ]);
    }

    public function show($id)
    {
        $row = Transfer::findOrFail($id);
        return View('dashboard.transfer.show', [
            'row' => $row,
            'type'=>'transfer',
            'action'=>'admin.transfer.update',
            'title'=>'حوالة بنكية',
            'status'=>true,
        ]);
    }

    public function accept($id){
        $row=$this->model->find($id);
        $row->update(
            [
                'status'=>'approved',
            ]
        );
        $user=User::find($row->user_id);
        if ($row->type=='charge'){
            $current_wallet=$user->wallet;
            $user->update([
                'wallet'=> $current_wallet+$row->amount
            ]);
            $data['title']='تم الموافقة على تحويلك البنكى بنجاح :) ';
        }else{
            $current_wallet=$user->wallet;
            $user->update([
                'wallet'=> $current_wallet-$row->amount
            ]);
            $data['title']='تم الموافقة على ارسال رسومك الى حسابك المرسل..ويرجى التواصل مع الادارة فى حالة وجود أية مشاكل باستردادك الرسوم :) ';
        }
        $data['receiver_id']=$user->id;
        $data['type']='admin';
        $data['admin_notify_type']='single';
        Notification::create($data);
        $row->refresh();
        return redirect()->back()->with('updated');
    }
    public function reject($id,Request $request){
        $row=$this->model->find($id);
        $row->update(
            [
                'status'=>'rejected',
            ]
        );
        $user=User::find($row->user_id);
        if ($row->type=='charge'){
            $data['title']='تم رفض تحويلك البنكى للسبب التالى : '.$request['reject_reason'];
        }else{
            $data['title']='تم رفض ارسال رسومك الى حسابك المرسل للسبب التالى : '.$request['reject_reason'];
        }
        $data['receiver_id']=$user->id;
        $data['type']='admin';
        $data['admin_notify_type']='single';
        Notification::create($data);
        $row->refresh();
        return redirect()->back()->with('updated');
    }
}
