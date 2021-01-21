<?php

namespace App\Http\Controllers;

use App\AuctionItem;
use App\AuctionUser;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){
        auth()->setDefaultDriver('api');
    }
    public function authUser(){
        try {
            $user=auth()->userOrFail();
        }catch (UserNotDefinedException $e){
            return response()->json(['error'=>$e->getMessage()]);
        }
        return $user;
    }
    public function auctionItemStatusUpdate(){
        $auction_items=AuctionItem::where('more_details->status','!=','paid')->get();
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
                    if ($auction_item->more_details['status']!='paid'){
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
                    }
                }elseif ($auction_item->item->auction_type_id==3){
                    $soon_winner=AuctionUser::where('item_id',$auction_item->item_id)->latest()->first();
                    if ($auction_item->more_details['status']!='paid'){
                        if ($soon_winner){
                            if ($auction_item->price < $auction_item->item->price){
                                $auction_item->update([
                                    'more_details'=>[
                                        'status'=>'negotiation'
                                    ]
                                ]);
                            }else{
                                $auction_item->update([
                                    'more_details'=>[
                                        'status'=>'paid'
                                    ]
                                ]);
                            }

                        }else{
                            $auction_item->update([
                                'more_details'=>[
                                    'status'=>'expired'
                                ]
                            ]);
                        }
                    }
                }else{
                    $soon_winner=AuctionUser::where('item_id',$auction_item->item_id)->latest()->first();
                    if ($auction_item->more_details['status']!='paid'){
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
