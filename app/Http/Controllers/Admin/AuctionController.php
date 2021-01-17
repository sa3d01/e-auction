<?php

namespace App\Http\Controllers\Admin;

use App\Auction;
use App\AuctionItem;
use App\Item;
use Carbon\Carbon;
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
        return View('dashboard.auction.create', [
            'type' => 'auction',
            'action' => 'admin.auction.store',
            'title' => 'أضافة مزاد',
            'create_fields' => ['موعد بداية المزاد' => 'start_date', 'مدة المزايدة على السلعة (بالثوانى)' => 'duration'],
            'multi_select' => [
                'rows' => Item::where(['status' => 'accepted', 'pay_status' => 1])->whereHas('reports')->where('auction_price', '!=', 'null')->get(),
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
        $auction = $this->model->create($data);
        $this->auction_items($auction);
        return redirect()->route('admin.auction.index')->with('created', 'تمت الاضافة بنجاح');
    }

    public function index()
    {
        $rows = $this->model->latest()->get();
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
            'index_fields' => ['الرقم التسلسلى' => 'id',
//                'العنوان' => 'name'
            ],
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
        $start_date = $auction->start_date;
        foreach ($auction->items as $item_id) {
            $seconds = $key * ($auction->duration);
            $item = Item::find($item_id);
            $item->update(['status' => 'shown']);
            $start_date = Carbon::createFromTimestamp($auction->start_date)->addSeconds($seconds)->timestamp;
            AuctionItem::create([
                'item_id' => $item_id,
                'auction_id' => $auction->id,
                'price' => $item->auction_price,
                'start_date' => $start_date,
            ]);
            $key++;
        }
        $end_date = Carbon::createFromTimestamp($start_date)->addSeconds($auction->duration)->timestamp;
        $auction->update([
            'more_details'=>[
                'end_date'=>$end_date
            ]
        ]);

    }
}
