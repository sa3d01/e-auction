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
        $data['vip']=new ItemCollection(Item::where('status','shown')->take(4)->get());
        $data['data']=new ItemCollection(Item::where('status','shown')->latest()->get());
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
