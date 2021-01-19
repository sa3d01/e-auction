<?php

namespace App\Http\Controllers\Api;

use App\AuctionItem;
use App\AuctionUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Notification;
use App\User;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class MasterController extends Controller
{
    protected $model;
    protected $auth_key;
    protected $purchasing_power_ratio;
    public function __construct()
    {
        $this->auctionItemStatusUpdate();
        parent::__construct();
    }

    public function sendResponse($result)
    {
        $response = [
            'status' => 200,
            'data' => $result,
        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $code = 400)
    {
        $response = [
            'status' => $code,
            'message' => $error,
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),$this->validation_rules(1),$this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data=$request->all();
        try {
            $data['user_id']=auth()->user()->id;
        }catch (UserNotDefinedException $e){

        }
        $this->model->create($data);
        return $this->sendResponse('تم الانشاء بنجاح');
    }

    public function auctionItemStatusUpdate(){
        $auction_items=AuctionItem::where('more_details->status','!=','paid')->where('more_details->status','!=','expired')->get();
        foreach ($auction_items as $auction_item){
            if ((Carbon::createFromTimestamp($auction_item->start_date) <= Carbon::now() )  &&  (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) >= Carbon::now())){
                $auction_item->update([
                    'more_details'=>[
                        'status'=>'live'
                    ]
                ]);
            }elseif (Carbon::createFromTimestamp($auction_item->start_date)->addSeconds($auction_item->auction->duration) < Carbon::now()){
                $auction_item->update([
                   'vip'=>'false'
                ]);
                if ($auction_item->item->auction_type_id==4 || $auction_item->item->auction_type_id==2){
                    $soon_winner=AuctionUser::where('item_id',$auction_item->item_id)->latest()->first();
                    if ($soon_winner){
                        $auction_item->update([
                            'more_details'=>[
                                'status'=>'negotiation'
                            ]
                        ]);
                    }else{
                        $auction_item->update([
                            'more_details'=>[
                                'status'=>'expired'
                            ]
                        ]);
                    }
                }elseif ($auction_item->item->auction_type_id==3){
                    $soon_winner=AuctionUser::where('item_id',$auction_item->item_id)->latest()->first();
                    if ($soon_winner && ($soon_winner->price < $auction_item->item->price)){
                        $auction_item->update([
                            'more_details'=>[
                                'status'=>'negotiation'
                            ]
                        ]);
                    }else{
                        $auction_item->update([
                            'more_details'=>[
                                'status'=>'expired'
                            ]
                        ]);
                    }
                }else{
                    $soon_winner=AuctionUser::where('item_id',$auction_item->item_id)->latest()->first();
                    if ($soon_winner){
                        $auction_item->update([
                            'more_details'=>[
                                'status'=>'paid'
                            ]
                        ]);
                    }else{
                        $auction_item->update([
                            'more_details'=>[
                                'status'=>'expired'
                            ]
                        ]);
                    }
                }
            }else{
                $auction_item->update([
                    'more_details'=>[
                        'status'=>'soon'
                    ]
                ]);
            }
        }
    }
}
