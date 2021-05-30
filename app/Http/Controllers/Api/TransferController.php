<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransferController extends MasterController
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
        parent::__construct();
    }
    private function upload_file($file){
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move('media/images/transfer/', $filename);
        return $filename;
    }
    public function transfer(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        if (Transfer::where(['user_id'=>$user->id,'status'=>0,'purchasing_type'=>'bank'])->latest()->first()){
            return $this->sendError('يرجى انتظار رد الإدارة على تحويلك السابق');
        }
        if ($request['type'] == 'purchasing_power' && $request['purchasing_type'] == 'online') {
            $data['purchasing_type']='online';
            Transfer::create($data);
            $user->update(['purchasing_power' =>$user->purchasing_power+ $request['money']]);
        }else{
            $data['purchasing_type']='bank';
            $filename = $this->upload_file($request->file('image'));
            $data['more_details']=[
                'image'=>$filename,
            ];
            Transfer::create($data);
        }
//        if ($request['type']=='buy_item'){
//            if (Transfer::where('more_details->item_id',$request['item_id'])->where('type','buy_item')->where('status',0)->latest()->first()){
//                return $this->sendError('يرجى انتظار رد الإدارة على تحويلك السابق');
//            }
//            $data['purchasing_type']='bank';
//            $filename = $this->upload_file($request->file('image'));
//            $data['more_details']=[
//                'item_id'=>$request['item_id'],
//                'image'=>$filename,
//            ];
//            Transfer::create($data);
//        }else{
//            if ($request['type'] == 'package') {
//                Package::findOrFail($request['package_id']);
//                Transfer::create($data);
//                $user->update(['package_id' => $request['package_id'], 'package_subscribed_at' => Carbon::now()]);
//            } elseif ($request['type'] == 'purchasing_power') {
//                Transfer::create($data);
//                $user->update(['purchasing_power' =>$user->purchasing_power+ $request['money']]);
//            } elseif ($request['type'] == 'wallet') {
//                Transfer::create($data);
//                $add_item_tax = Setting::first()->value('add_item_tax');
//                $wallet = $user->wallet + $request['money'];
//                foreach ($user->items as $item) {
//                    if (($item->pay_status == 0) && ($add_item_tax < $wallet)) {
//                        $item->update(['pay_status' => 1]);
//                        $wallet = $wallet - $add_item_tax;
//                    }
//                }
//                $user->update(['wallet' => $wallet]);
//            }
//        }
        $data = new UserResource($user);
        $token = auth()->login($user);
        return $this->sendResponse($data)->withHeaders(['apiToken' => $token, 'tokenType' => 'bearer']);
    }
    public function refund(Request $request){
        $user = auth()->user();
        if (Transfer::where(['type'=>$request['type'],'status'=>0,'user_id'=>$user->id])->latest()->first()){
            return $this->sendError('يرجى انتظار رد الإدارة على طلبك السابق');
        }
        $data['user_id']=$user->id;
        $data['money']=$request['money'];
        $data['type']=$request['type'];
        $data['purchasing_type']='bank';
        $data['more_details']=[
            'name'=>$request['name'],
            'account_number'=>$request['account_number'],
            'bank_name'=>$request['bank_name'],
        ];
        Transfer::create($data);
        $data = new UserResource($user);
        return $this->sendResponse($data);
    }

}
