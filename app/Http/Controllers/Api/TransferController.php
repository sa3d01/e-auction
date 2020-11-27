<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Package;
use App\Transfer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransferController extends MasterController
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function transfer(Request $request){
        $user = auth()->user();
        $data=$request->all();
        $data['user_id']=$user->id;
        if ($request['type'] == 'package'){
            Package::findOrFail($request['package_id']);
            Transfer::create($data);
            $user->update(['package_id'=>$request['package_id'],'package_subscribed_at'=>Carbon::now()]);
        }elseif ($request['type'] == 'purchasing_power'){
            Transfer::create($data);
            $user->update(['purchasing_power'=>$request['money']]);
        }
        $data= new UserResource($user);
        $token = auth()->login($user);
        return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
    }

}
