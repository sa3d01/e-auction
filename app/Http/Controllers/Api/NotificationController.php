<?php

namespace App\Http\Controllers\Api;

use App\Contact;
use App\DropDown;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Notification;
use App\Order;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class NotificationController extends MasterController
{
    protected $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
        parent::__construct();
    }


    public function show($id)
    {
        if (!$this->model->find($id))
            return $this->sendError('not found');
        $single=$this->model->find($id);
        $single->update([
            'read'=>'true'
        ]);
        return $this->sendResponse(NotificationResource::make($this->model->find($id)));
    }
    public function index()
    {
        $notifies=new NotificationCollection($this->model->where('receiver_id',\request()->user()->id)->latest()->get());
        $unread=$this->model->where('receiver_id',\request()->user()->id)->where('read','false')->count();
        return $this->sendResponse(['data'=>$notifies,'unread'=>$unread]);
    }



}
