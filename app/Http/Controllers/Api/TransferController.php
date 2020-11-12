<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Package;
use App\User;
use App\userType;
use App\Wallet;
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
        if ($request['package_id']){
            Package::findOrFail($request['package_id']);
            $user->update(['package_id'=>$request['package_id'],'package_subscribed_at'=>Carbon::now()]);
        }elseif ($request['purchasing_power']){
            $user->update(['purchasing_power'=>$request['purchasing_power']]);
        }
        $data= new UserResource($user);
        $token = auth()->login($user);
        return $this->sendResponse($data)->withHeaders(['apiToken'=>$token,'tokenType'=>'bearer']);
    }

}
