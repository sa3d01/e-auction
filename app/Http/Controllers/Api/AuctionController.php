<?php

namespace App\Http\Controllers\Api;

use App\Auction;
use App\AuctionItem;
use App\DropDown;
use App\Http\Resources\AuctionCollection;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ReportCollection;
use App\Item;
use App\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuctionController extends MasterController
{
    protected $model;

    public function __construct(Auction $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(){
        $vip_items=AuctionItem::where('vip','true')->pluck('item_id');
        $data['vip']=new ItemCollection(Item::whereIn('id',$vip_items)->latest()->get());
        $data['data']=new ItemCollection(Item::where('status','shown')->orWhere('status','sold')->latest()->get()->uniqe('item_id'));
        return $this->sendResponse($data);
    }
    public function auctions(){
        $auctions=Auction::where('more_details->end_date','>',Carbon::now()->timestamp)->get();
        return $this->sendResponse(new AuctionCollection($auctions));
    }
    public function auctionItems($auction_id){
        $auction_items=AuctionItem::where('auction_id',$auction_id)->pluck('item_id');
        return $this->sendResponse(new ItemCollection(Item::whereIn('id',$auction_items)->latest()->get()));
    }
    public function search(Request $request){
        if ($request['name']){
            $marks=DropDown::whereClass('Mark')->where('name','LIKE','%'.$request['name'].'%')->pluck('id');
            $models=DropDown::whereClass('Model')->where('name','LIKE','%'.$request['name'].'%')->pluck('id');
            $data=new ItemCollection(Item::whereIn('mark_id',$marks)->orWhereIn('model_id',$models)->latest()->get());
//            $data=new ItemCollection(Item::where('name','LIKE','%'.$request['name'].'%')->latest()->get());
        }else{
            $q=Item::query();
            if ($request['from_date'] && $request['to_date']){
                $from=$request['from_date'];
                $to=$request['to_date'];
                $ids=AuctionItem::whereBetween('start_date', [$from, $to])->pluck('item_id');
                $q=$q->whereIn('id',$ids);
            }
            foreach ($request->input() as $key=>$value){
                if ($value=='' || $value==null || $key=='from_date' || $key=='to_date') continue;
                $q=$q->where($key,$value);
            }
            $data=new ItemCollection($q->latest()->get());
        }
        return $this->sendResponse($data);
    }
    public function show($id){
        $item=Item::find($id);
        $date['item']=new ItemResource($item);
        $date['similar']=new ItemCollection(Item::where('id','!=',$id)->where(['status'=>'shown','mark_id'=>$item->mark_id])->take(4)->get());
        return $this->sendResponse($date);
    }
    public function reports($id){
        $date['item']=new ItemResource(Item::find($id));
        $date['reports']=new ReportCollection(Report::where('item_id',$id)->get());
        return $this->sendResponse($date);
    }

}
