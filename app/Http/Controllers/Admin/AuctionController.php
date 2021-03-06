<?php

namespace App\Http\Controllers\Admin;

use App\Auction;
use App\AuctionItem;
use App\Item;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;

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
        $q_items=Item::query();
//        $q_items=$q_items->where('pay_status',1);
//        $q_items=$q_items->whereHas('reports');
//        $q_items=$q_items->where('auction_price', '!=', 'null');
        $q_items = $q_items->where(function($query) {
            $query->where('status','delivered');
//                ->orWhere('status','expired');
        });
        return View('dashboard.auction.create', [
            'type' => 'auction',
            'action' => 'admin.auction.store',
            'title' => 'أضافة مزاد',
            'create_fields' => ['موعد بداية المزاد' => 'start_date', 'مدة المزايدة على السلعة (بالثوانى)' => 'duration'],
            'multi_select' => [
                'rows' => $q_items->get(),
                'title' => 'السلع',
                'input_name' => 'items'
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1), $this->validation_msg());
        if ($request['start_date'] < Carbon::now()) {
            return redirect()->back()->withErrors('تأكد من اختيار تاريخ صحيح');
        }
        $data = $request->all();
        $data['items'] = $request['items'];
        if (gettype($request['items']) == 'string') {
            $data['items'] = explode(',', $request['items']);
        }
        $q_items=Item::query();
        $q_items = $q_items->where(function($query) {
            $query->where('status','delivered');
        })->pluck('id')->toArray();
        foreach ($data['items'] as $datum){
            if (!in_array($datum,$q_items)){
                return redirect()->back()->withErrors('قم بتحديث الصفحه');
            }
        }
        $auction = $this->model->create($data);
        $this->auction_items($auction);
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => null,
            'data' => [
                'title' => '',
                'body' => '',
                'type'=>'new_auction',
                'db'=>false,
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->sendByTopic('new_auction')
            ->send();
        return redirect()->route('admin.auction.index')->with('created', 'تمت الاضافة بنجاح');
    }

    public function index()
    {
        $rows = $this->model->orderBy('start_date','desc')->get();
        return View('dashboard.auction.index', [
            'rows' => $rows,
            'type' => 'auction',
            'title' => 'قائمة المزادات',
            'index_fields' => ['الرقم التسلسلى' => 'id', 'موعد بداية المزاد' => 'start_date', 'مدة المزايدة بالثوانى' => 'duration'],
        ]);
    }

    public function items($auction_id)
    {
        $auction = Auction::find($auction_id);
        $rows = Item::whereIn('id', $auction->items)->latest()->get();
        return View('dashboard.item.index', [
            'rows' => $rows,
            'status' => 'shown',
            'type' => 'item',
            'title' => 'قائمة السلع ',
            'index_fields' => ['الرقم التسلسلى' => 'id'],
            'selects' => [
                [
                    'name' => 'user',
                    'title' => 'المستخدم'
                ],
                [
                    'name' => 'auction_type',
                    'title' => 'نوع المزايدة'
                ],
            ],
        ]);
    }

    public function auction_items($auction)
    {
        $key = 0;
        foreach ($auction->items as $item_id) {
            $item = Item::find($item_id);
            $item->update(['status' => 'shown']);
            if ($item->auction_price==null){
                $item_auction_price=0;
            }else{
                $item_auction_price=$item->auction_price;
            }
            $seconds = $key * ($auction->duration);
            $start_date = Carbon::createFromTimestamp($auction->start_date)->addSeconds($seconds)->timestamp;
            AuctionItem::create([
                'item_id' => $item_id,
                'auction_id' => $auction->id,
                'price' => $item_auction_price,
                'start_date' => $start_date,
                'more_details'=>[
                    'status'=>'soon'
                ]
            ]);
            $key++;
        }
        $end_date = Carbon::createFromTimestamp($auction->start_date)->addSeconds(count($auction->items)*$auction->duration)->timestamp;
        $auction->update([
            'more_details'=>[
                'end_date'=>$end_date
            ]
        ]);

    }
}
