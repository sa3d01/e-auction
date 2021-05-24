<?php

namespace App\Http\Controllers\Admin;

use App\AuctionItem;
use App\Contact;
use App\FeedBack;
use App\Http\Controllers\Controller;
use App\Item;
use App\Notification;
use App\Setting;
use App\User;
use Illuminate\Http\Request;

abstract class MasterController extends Controller
{

    protected $model;
    protected $route;
    protected $module_name;
    protected $single_module_name;
    protected $index_fields;
    protected $show_fields;
    protected $create_fields;
    protected $update_fields;
    protected $json_fields;

    public function __construct()
    {
        $users_count = User::count();
        $new_items_count = Item::where('status', 'pending')->count();
        $new_contacts_count = Contact::where(['read' => 'false'])->count();
        $new_feed_backs_count = FeedBack::where(['status' => 'pending'])->count();
        $this->auctionItemStatusUpdate();
        $pre_auction_items = 0;
        $expire_auction_items = 0;
        $live_auction_items = 0;
        foreach (Item::all() as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->latest()->first();
//            $auction_items = AuctionItem::where('auction_id', $auction_item->auction_id)->count();
            if ($auction_item){
                if ($auction_item->more_details['status']=='soon'){
                    $pre_auction_items++;
                }elseif ($auction_item->more_details['status']=='live'){
                    $live_auction_items++;
                }else{
                    $expire_auction_items++;
                }
            }
//            if (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_items * $auction_item->auction->duration) < Carbon::now()) {
//                $expire_auction_items++;
//            } elseif ((Carbon::createFromTimestamp($auction_item->start_date) <= Carbon::now()) && (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_items * $auction_item->auction->duration) >= Carbon::now())) {
//                $live_auction_items++;
//            } else {
//                $pre_auction_items++;
//            }
        }

        $this->middleware('auth:admin');
        view()->share(array(
            'module_name' => $this->module_name,
            'single_module_name' => $this->single_module_name,
            'route' => $this->route,
            'index_fields' => $this->index_fields,
            'show_fields' => $this->show_fields,
            'create_fields' => $this->create_fields,
            'update_fields' => $this->update_fields,
            'json_fields' => $this->json_fields,
            'settings' => Setting::first(),
            'users_count' => $users_count,
            'new_contacts_count' => $new_contacts_count,
            'new_items_count' => $new_items_count,
            'new_contacts' => Contact::where('read', 'false')->get(),
            'new_feed_backs_count' => $new_feed_backs_count,
            'pre_auction_items' => $pre_auction_items,
            'expire_auction_items' => $expire_auction_items,
            'live_auction_items' => $live_auction_items,
            'admin_notifications' => Notification::where(['receiver_id' => null,'receivers' => null, 'read' => 'false'])->latest()->get(),
        ));
    }

    public function index()
    {
        $rows = $this->model->latest()->get();
        return view('admin.' . $this->route . '.index', compact('rows'));
    }

    public function create()
    {
        return view('admin.' . $this->route . '.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1), $this->validation_msg());
        $this->model->create($request->all());
        return redirect('admin/' . $this->route . '')->with('created', 'تمت الاضافة بنجاح');
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        return View('admin.' . $this->route . '.edit', compact('row'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, $this->validation_func(2, $id), $this->validation_msg());
        $obj = $this->model->find($id);
        $obj->update($request->all());
        return redirect()->back()->with('updated', 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $model=$this->model->findOrFail($id);
        $model->delete();
        return redirect()->back()->with('deleted', 'تم الحذف بنجاح');
    }

    public function show($id)
    {
        $row = $this->model->find($id);
        return View('admin.' . $this->route . '.show', compact('row'));
    }
}

