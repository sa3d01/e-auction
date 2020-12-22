<?php

namespace App\Http\Controllers\Admin;

use App\Auction;
use App\Http\Resources\ItemResource;
use App\Http\Resources\OrderResource;
use App\Item;
use App\Notification;
use App\Report;
use App\Setting;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuctionController extends MasterController
{
    public function __construct(Auction $model)
    {
        $this->model = $model;
        $this->route = 'auction';
        parent::__construct();
    }
    public function validation_func($method, $id = null)
    {
        return [
            'start_date' => 'required',
            'duration' => 'required',
            'items' => 'required',
        ];
    }

    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
        );
    }
    public function create()
    {
        //todo : order items
        return View('dashboard.create.create', [
            'type'=>'auction',
            'action'=>'admin.auction.store',
            'title'=>'أضافة مزاد',
            'create_fields'=>['موعد بداية المزاد' => 'start_date','مدة المزايدة على السلعة (بالثوانى)'=>'duration'],
            'multi_select'=>[
                'rows'=>Item::where(['status'=>'accepted','pay_status'=>1])->whereHas('reports')->where('auction_price','!=','null')->get(),
                'title'=>'السلع',
                'input_name'=>'items'
            ],
        ]);
    }
    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1),$this->validation_msg());
        $now=Carbon::now();
        if($request['start_date'] < $now){
            return redirect()->back()->withErrors('تأكد من اختيار تاريخ صحيح');
        }
        foreach ($request['items'] as $item_id){
            Item::find($item_id)->update(['status'=>'shown']);
        }
        $this->model->create($request->all());
        return redirect()->route('admin.auction.index')->with('created', 'تمت الاضافة بنجاح');
    }
    public function index()
    {
        $rows=$this->model->latest()->get();
        return View('dashboard.index.index', [
            'rows' => $rows,
            'type'=>'auction',
            'title'=>'قائمة المزادات',
            'index_fields'=>['الرقم التسلسلى' => 'id','موعد بداية المزاد'=>'start_date','مدة المزايدة بالثوانى'=>'duration'],
        ]);
    }

}
