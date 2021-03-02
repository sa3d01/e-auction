<?php

namespace App\Http\Controllers\Api;

use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\ProfileUpdateRequest;
use App\Http\Requests\Api\Auth\ResendPhoneVerificationRequest;
use App\Http\Requests\Api\Auth\VerifyPhoneRequest;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\paidItemCollection;
use App\Http\Resources\UserResource;
use App\Item;
use App\Transfer;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UserController extends MasterController
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function authPhoneAndMail(LoginRequest $request):object
    {
        if (!$request->has('email') && !$request->has('phone')) {
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        $activation_code = rand(1111, 9999);
        if ($request->has('email')){
            $user = User::where('email', $request['email'])->first();
        }else{
            $user = User::where('phone', $request['phone'])->first();
        }
        $all = $request->all();
        $all['activation_code'] = $activation_code;
        if (!$user) {
            $all['wallet']=10000;
            User::create($all);
        } else {
            $user->update($all);
        }
        $this->send_code($activation_code, $request['email'], $request['phone']);
        return $this->sendResponse(['activation_code' => $activation_code]);
    }

    public function validation_messages()
    {
        return array(
            'unique' => ' مسجل بالفعل :attribute هذا الـ',
            'required' => ':attribute يجب ادخال الـ',
            'max' => ' يجب أﻻ تزيد قيمته عن :max عناصر :attribute',
            'min' => ' يجب أﻻ تقل قيمته عن :min عناصر :attribute',
            'email' => 'يرجى التأكد من صحة البريد الالكترونى',
            'regex' => 'تأكد من أن رقم الجوال صحيح ويبدا بـ 05 '
        );
    }

    function send_code($activation_code, $email = null, $phone = null)
    {
        if ($email) {
            $this->sendToMail($email, $activation_code);
        }
        if ($phone) {
            $this->sendToPhone($phone, $activation_code);
        }
    }

    function sendToMail($email, $activation_code)
    {
//        Mail::to($email)->send(new ConfirmCode($activation_code));
    }
    private function buildHttpClient()
    {
        $endpoint = 'https://www.hisms.ws/';
        return new Client(['base_uri' => $endpoint]);
    }
    function sendToPhone($phone, $activation_code)
    {
        $normalizedPhone = substr($phone, 1); // remove +
        $client = $this->buildHttpClient();
        $response = $client->request('GET', 'api.php', [
            'query' => [
                'username' => "966595073103",
                'password' => "H123m456",
                'numbers' => $normalizedPhone,
                'sender' => "Active-code",
                'message' => $activation_code,
                'send_sms' => true,
            ]
        ]);
        $array = json_decode($response->getBody(), true);
        return $array;
    }

    public function verifyUser(VerifyPhoneRequest $request)
    {
        if (!$request->has('email') && !$request->has('phone')) {
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        if ($request->has('email')){
            $user = User::where('email', $request['email'])->first();
        }else{
            $user = User::where('phone', $request['phone'])->first();
        }
        if (!$user) {
            return $this->sendError('المستخدم غير مسجل');
        }
        if ($user->activation_code === $request['activation_code']) {
            $data = new UserResource($user);
            if ($request->has('email')) {
                $user->update([
                    'activation_code' => null,
                    'email_verified_at' => Carbon::now()
                ]);
            } else {
                $user->update([
                    'activation_code' => null,
                    'phone_verified_at' => Carbon::now()
                ]);
            }
            $token = auth()->login($user);
            return $this->sendResponse($data)->withHeaders(['apiToken' => $token, 'tokenType' => 'bearer']);
        } else {
            return $this->sendError('كود التفعيل غير صحيح');
        }
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();
        $user->update($request->except(['package_id', 'wallet','purchasing_power']));
        $data = new UserResource($user);
        $token = auth()->login($user);
        return $this->sendResponse($data)->withHeaders(['apiToken' => $token, 'tokenType' => 'bearer']);
    }

    public function resendCode(ResendPhoneVerificationRequest $request)
    {
        if (!$request->has('email') && !$request->has('phone')) {
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        if ($request->has('email')){
            $user = User::where('email', $request['email'])->first();
        }else{
            $user = User::where('phone', $request['phone'])->first();
        }
        if (!$user) {
            return $this->sendError('المستخدم غير مسجل');
        }
        $activation_code = rand(1111, 9999);
        $this->send_code($activation_code, $request['email'], $request['phone']);
        $user->update(['activation_code' => $activation_code]);
        return $this->sendResponse(['activation_code' => $activation_code]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'device' => [
                'id' => null,
                'type' => null,
            ]
        ]);
        auth()->logout();
        return $this->sendResponse('');
    }

    public function profile()
    {
        $user = auth()->user();
        $data['user'] = new UserResource($user);
        $my_items=Item::where('user_id', $user->id)->latest()->get();
        $data['my_items'] = new ItemCollection($my_items);
        $auction_users = AuctionUser::where('user_id', $user->id)->pluck('item_id');
        $data['my_auctions'] = new ItemCollection(Item::whereIn('id', $auction_users)->latest()->get());
        return $this->sendResponse($data);
    }

    public function myPaidItems(){
        $user = auth()->user();
        $paid_auction_items=AuctionItem::where('more_details->status','paid')->orWhere('more_details->status','delivered')->get();
        $item_ids=[];
        foreach ($paid_auction_items as $paid_auction_item){
            $winner=AuctionUser::where(['auction_id'=>$paid_auction_item->auction_id,'item_id'=>$paid_auction_item->item_id])->latest()->value('user_id');
            if ($winner==$user->id){
                $item_ids[]=$paid_auction_item->item_id;
            }
        }
        return $this->sendResponse(new paidItemCollection(Item::whereIn('id',$item_ids)->latest()->get()));
    }

    public function auctionReports()
    {
        $user = auth()->user();
        $data = [];
        $transfers = Transfer::where('user_id', $user->id)->latest()->get();
        foreach ($transfers as $transfer) {
            $arr['type'] = $transfer->type;
            $arr['money'] = $transfer->money;
            $arr['time'] = Carbon::parse($transfer->created_at)->diffForHumans();
            $data[] = $arr;
        }
        return $this->sendResponse($data);
    }

    public function productsReports()
    {
        $user = auth()->user();
        $pre_auction_items = 0;
        $live_auction_items = 0;
        $user_items_ids = $user->items()->pluck('id');
        $paid_auction_items = AuctionItem::whereIn('item_id', $user_items_ids)->where(function($query) {
            $query->where('more_details->status', 'paid')
                ->orWhere('more_details->status', 'delivered');
        })->get();
        $money = 0;
        foreach ($paid_auction_items as $paid_auction_item) {
            $money += $paid_auction_item->price;
        }
        foreach ($user->items() as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->latest()->first();
            if ((Carbon::createFromTimestamp($auction_item->auction->more_details['end_date']) >= Carbon::now()) && (Carbon::createFromTimestamp($auction_item->auction->start_date) <= Carbon::now())) {
                $live_auction_items++;
            } else {
                $pre_auction_items++;
            }
        }
        $data = [];
        $data['money'] = $money;
        $data['success_paid'] = count($paid_auction_items);
        $data['rejected_items'] = $user->items()->where('status', 'rejected')->count();
        $data['pre_auction_items'] = $pre_auction_items;
        $data['live_auction_items'] = $live_auction_items;
        return $this->sendResponse($data);
    }

    public function show($id)
    {
        $user = User::find($id);
        $data = new UserResource($user);
        return $this->sendResponse($data);
    }

    public function favourite()
    {
        $item_ids = Favourite::where('user_id', \request()->user()->id)->pluck('item_id');
        $items = new ItemCollection(Item::whereIn('id', $item_ids)->latest()->get());
        return $this->sendResponse($items);
    }
}
