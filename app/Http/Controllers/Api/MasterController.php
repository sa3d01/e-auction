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
}
