<?php

namespace App\Http\Controllers\Api;

use App\Auction;
use App\Http\Resources\DropDownCollection;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ReportCollection;
use App\Item;
use App\Report;

class AuctionController extends MasterController
{
    protected $model;

    public function __construct(Auction $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(){
//        $active_auctions=Auction::where('active',1)->get();
        $items_query=Item::where('status','shown');
//        foreach ($active_auctions as $auction){
//            $items_query=$items_query->whereIn('id',$auction->items);
//        }



        $data['vip']=new ItemCollection($items_query->take(4)->get());
        $data['data']=new ItemCollection($items_query->get());
        return $this->sendResponse($data);
    }
    public function show($id){
        $date['item']=new ItemResource(Item::find($id));
        $date['similar']=new ItemCollection(Item::where('status','shown')->take(4)->get());
        return $this->sendResponse($date);
    }
    public function reports($id){
        $date['item']=new ItemResource(Item::find($id));
        $date['reports']=new ReportCollection(Report::where('item_id',$id)->get());
        return $this->sendResponse($date);
    }

}
