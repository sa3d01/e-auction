<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ItemResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Item;
use App\Package;
use App\Transfer;
use App\User;
use App\userType;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ItemController extends MasterController
{
    protected $model;

    public function __construct(Item $model)
    {
        $this->model = $model;
        parent::__construct();
    }
    public function uploadImages(Request $request)
    {
        $validate = Validator::make($request->all(),
            [
                'images' => 'required',
                 'images.*' => 'image|mimes:jpeg,jpg,png,jpg,gif,svg|max:6048'
            ]
        );
        if ($validate->fails()) {
            return $this->sendError('يوجد مشكلة بالصور المرفقة');
        }
        $data = [];
        for ($i = 0; $i < count($request['images']); $i++) {
            $file = $request['images'][$i];
            $destinationPath = 'media/images/item/';
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $data[] = asset($destinationPath) . '/' . $filename;
        }
        return $this->sendResponse($data);
    }
    public function store(Request $request){
        $user = auth()->user();
        $data=$request->all();
        $data['user_id']=$user->id;
        $item=$this->model->create($data);
        return $this->sendResponse('تم ارسال طلب إضافة المنتج بنجاح');
    }

}
