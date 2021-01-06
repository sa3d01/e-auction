<?php

namespace App\Http\Controllers\Api;

use App\Favourite;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Item;
use App\Package;
use App\Setting;
use App\Transfer;
use App\User;
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
                 'images.*' => 'image|mimes:jpeg,jpg,png,jpg,gif,svg'
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
        $items_images=[];
        if ($request->images){
            $file=$request->images[0];
            $filename=null;
            if (is_file($file)) {
                $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move('media/images/item/', $filename);
            }else {
                $filename = $file;
            }
            $items_images[]=$filename;
            $data['images'] = $items_images;
        }
        $item=$this->model->create($data);
        $add_item_tax=Setting::first()->value('add_item_tax');
        if ($add_item_tax < $user->wallet){
            $item->update(['pay_status'=>1]);
            $wallet=$user->wallet-$add_item_tax;
            $user->update(['wallet'=>$wallet]);
        }
        return $this->sendResponse('تم ارسال طلب إضافة المنتج بنجاح');
    }
    public function favouriteModification($item_id){
        $is_favourite=Favourite::where(['user_id'=>\request()->user()->id, 'item_id'=>$item_id])->first();
        if ($is_favourite){
            $is_favourite->delete();
            return $this->sendResponse('تم الحذف من المفضلة');

        }else{
            Favourite::create([
                'user_id'=>\request()->user()->id,
                'item_id'=>$item_id
            ]);
            return $this->sendResponse('تمت الإضافة بنجاح');
        }
    }
}
