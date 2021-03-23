<?php

namespace App\Http\Controllers\Api;

use App\Favourite;
use App\Item;
use App\Notification;
use App\Setting;
use App\Transfer;
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
    public function uploadImages(Request $request):object
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
    public function store(Request $request):object{
        $user = auth()->user();
        if ($user->profileIsFilled()==false){
            return $this->sendError('يجب اكمال بيانات ملفك الشخصى أولا');
        }
        if (Transfer::where(['user_id'=>$user->id,'type'=>'refund_wallet','status'=>0])->first()){
            return $this->sendError(' محفظتك معلقة حاليا لحين رد الإدارة .');
        }
        $data=$request->all();
        $data['user_id']=$user->id;
        $items_images=[];
        if ($request->images){
            $file=$request->images[0];
            $filename=null;
            if (is_file($file)) {
                $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move('media/images/item/', $filename);
                $local_name=asset('media/images/item/').'/'.$filename;
            }else {
                $local_name = $file;
            }
            $items_images[]=$local_name;
            $data['images'] = $items_images;
        }
        $item=$this->model->create($data);
        $title['ar'] = 'تم إضافة سلعة جديدة عن طريق مستخدم رقم '. $user->id;
        $this->new_item_notify_admin($title,$item);
        return $this->sendResponse('تم ارسال طلب إضافة المنتج بنجاح');
    }
    public function favouriteModification($item_id):object{
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
    public function new_item_notify_admin($title,$item){
        $data['title']=$title;
        $data['item_id']=$item->id;
        $data['type']='admin';
        $data['admin_notify_type']='all';
        Notification::create($data);
    }
}
