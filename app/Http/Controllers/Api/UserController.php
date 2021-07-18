<?php

namespace App\Http\Controllers\Api;

use App\Auction;
use App\AuctionItem;
use App\AuctionUser;
use App\Favourite;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\PhoneUpdateRequest;
use App\Http\Requests\Api\Auth\ProfileUpdateRequest;
use App\Http\Requests\Api\Auth\ResendPhoneVerificationRequest;
use App\Http\Requests\Api\Auth\SubmitPhoneUpdateRequest;
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
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends MasterController
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function authPhoneAndMail(LoginRequest $request): object
    {
        if (!$request->has('email') && !$request->has('phone')) {
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        $activation_code = rand(1111, 9999);
        if ($request->has('email') && $request->has('phone')) {
            $user = User::where(['email' => $request['email'], 'phone' => $request['phone']])->first();
//            if (!$user)
//                return $this->sendError('البريد الالكترونى أو رقم الهاتف مسجل من قبل');
        } elseif ($request->has('email')) {
            $user = User::where('email', $request['email'])->first();
        } else {
            $user = User::where('phone', $request['phone'])->first();
        }
        $all = $request->all();
        $all['activation_code'] = $activation_code;
        if (!$user) {
//            $all['wallet']=10000;
            $all['package_id'] = 5;
            User::create($all);
        } else {
            if ($user->status == 0) {
                return $this->sendError('تم حظرك من قبل إدارة التطبيق ..');
            }
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
//        if ($email) {
//            //$this->sendToMail($email, $activation_code);
//        }
        if ($phone) {
            $this->sendToPhone($phone, $activation_code);
        }
    }

    function sendToMail($email, $activation_code)
    {
        //Mail::to($email)->send(new SendCode($activation_code));
    }

//    private function buildHttpClient()
//    {
//        $endpoint = 'https://www.hisms.ws/';
//        return new Client(['base_uri' => $endpoint]);
//    }

    protected function buildHttpClient()
    {
        $endpoint = 'https://eu250.chat-api.com/';
        return new Client([
            'base_uri' => $endpoint,
        ]);
    }

    function sendToPhone($phone, $activation_code)
    {
        try {
            $client = $this->buildHttpClient();
            $response = $client->request('POST', 'instance304158/sendMessage?token=17u577kh4wcj4cjg', [
                'query' => [
                    'token' => '17u577kh4wcj4cjg',
                    'phone' => substr($phone, 1),
                    'body' => "E-Auction verification code is '" . $activation_code . "'",
                ]
            ]);
            $array = json_decode($response->getBody(), true);
        }catch (\Exception $e){

        }
    }

    public function verifyUser(VerifyPhoneRequest $request)
    {
        if (!$request->has('email') && !$request->has('phone')) {
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        if ($request->has('email')) {
            $user = User::where('email', $request['email'])->first();
        } else {
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
        $data = $request->except(['package_id', 'wallet', 'purchasing_power']);
        $data['more_details'] = [
            'bank' => [
                'bank_name' => $request['bank_name'],
                'iban_number' => $request['iban_number'],
                'account_number' => $request['account_number'],
            ]
        ];
        $user->update($data);
        $user_model = new UserResource($user);
        $token = auth()->login($user);
        return $this->sendResponse($user_model)->withHeaders(['apiToken' => $token, 'tokenType' => 'bearer']);
    }

    public function requestUpdatePhone(PhoneUpdateRequest $request)
    {
        $user = auth()->user();
        $activation_code = rand(1111, 9999);
        $user->update([
            'activation_code'=>$activation_code,
            'phone_details'=>[
                'new_phone'=>$request['new_phone']
            ]
        ]);
        $this->sendToPhone($request['new_phone'],$activation_code);
        return $this->sendResponse([
            'new_phone'=>$request['new_phone'],
            'activation_code' => $activation_code
        ]);
    }

    public function updatePhone(SubmitPhoneUpdateRequest $request)
    {
        $user = auth()->user();
        if ($user->activation_code === $request['activation_code']) {
            $data = new UserResource($user);
            $user->update([
                'activation_code' => null,
                'phone_details' => [
                    'old_phone'=>$user->phone
                ],
                'phone' => $request['new_phone'],
                'phone_verified_at' => Carbon::now()
            ]);
            $token = auth()->login($user);
            return $this->sendResponse($data)->withHeaders(['apiToken' => $token, 'tokenType' => 'bearer']);
        } else {
            return $this->sendError('كود التفعيل غير صحيح');
        }
    }

    public function resendCode(ResendPhoneVerificationRequest $request)
    {
        if (!$request->has('email') && !$request->has('phone')) {
            return $this->sendError('يجب ادخال وسيلة ارسال واحدة على الأقل');
        }
        if ($request->has('email')) {
            $user = User::where('email', $request['email'])->first();
        } else {
            $user = User::where('phone', $request['phone'])->first();
        }
        if (!$user) {
            return $this->sendError('المستخدم غير مسجل');
        }
        $activation_code =rand(1111, 9999);
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
//        $my_items=Item::where('user_id', $user->id)->latest()->get();
//        $data['my_items'] = new ItemCollection($my_items);
//        $auction_users = AuctionUser::where('user_id', $user->id)->pluck('item_id');
//        $data['my_auctions'] = new ItemCollection(Item::whereIn('id', $auction_users)->latest()->get());
        return $this->sendResponse($data);
    }

    public function userItems()
    {
        $user = auth()->user();
        $my_items = Item::where('user_id', $user->id)->latest()->get();
        $data = [];
        foreach ($my_items as $my_item) {
            $auction_item = AuctionItem::where('item_id', $my_item->id);
            $auction = Auction::whereJsonContains('items', $my_item->id)->where('more_details->end_date', '<', Carbon::now()->timestamp)->latest()->first();
            if ($auction) {
                $auction_item = $auction_item->where('auction_id', $auction->id)->latest()->first();
            } else {
                $auction_item = $auction_item->latest()->first();
            }
            $is_favourite = false;
            $favourite = Favourite::where(['user_id' => \request()->user()->id, 'item_id' => $my_item->id])->first();
            if ($favourite) {
                $is_favourite = true;
            }
            $arr['status_text'] = '';
            $arr['bid_count'] = 0;
            $arr['can_bid'] = false;
            if ($auction_item) {
                $features = $auction_item->auctionTypeFeatures(auth()->user()->id);
                $arr['auction_status'] = $features['status'];
                $arr['negotiation'] = $features['negotiation'];
                $arr['direct_pay'] = $features['direct_pay'];
                $arr['user_price'] = $features['user_price'];
                $arr['live'] = $features['live'];
                $arr['is_paid'] = false;
                if ($features['status'] == 'paid') {
                    $arr['status_text'] = $this->lang() == 'ar' ? 'مغلق' : 'closed';
                    if (Transfer::where('more_details->item_id', $my_item->id)->where('type', 'buy_item')->where('status', 0)->latest()->first()) {
                        $arr['is_paid'] = true;
                        $arr['status_text'] = $this->lang() == 'ar' ? 'مغلق' : 'closed';
                    }
                } elseif ($features['status'] == 'delivered') {
                    $arr['is_paid'] = true;
                    $arr['status_text'] = $this->lang() == 'ar' ? 'مغلق' : 'closed';
                } elseif ($features['status'] == 'negotiation') {
                    $arr['is_paid'] = true;
                    $arr['status_text'] = $this->lang() == 'ar' ? 'مغلق' : 'closed';
                } elseif ($features['status'] == 'soon') {
                    $arr['status_text'] = $this->lang() == 'ar' ? 'تم جدولتها لمزاد' : 'prepared for auction';
                } elseif ($features['status'] == 'expired') {
                    $arr['status_text'] = $this->lang() == 'ar' ? 'مغلق' : 'closed';
                } elseif ($features['status'] == 'live') {
                    $arr['status_text'] = $this->lang() == 'ar' ? 'مباشر' : 'live';
                }
                $arr['auction_type'] = $my_item->auction_type->name[$this->lang()];
                $arr['start_date'] = $auction_item->auction->start_date;
                $arr['now_date'] = Carbon::now()->format('Y-m-d h:i:s A');
                $arr['end_string_date'] = Carbon::createFromTimestamp($auction_item->auction->more_details['end_date'])->format('Y-m-d h:i:s A');
                $arr['start_string_date'] = Carbon::createFromTimestamp($auction_item->auction->start_date)->format('Y-m-d h:i:s A');
                $arr['auction_duration'] = $auction_item->auction->duration;
                $arr['auction_price'] = $auction_item->price;
                $arr['bid_count'] = (int)AuctionUser::where(['auction_id' => $auction_item->auction_id, 'item_id' => $auction_item->item_id])->count();
            } else {
                if ($my_item->status == 'pending') {
                    $arr['auction_status'] = 'تم طلب الاضافة';
                    $arr['status_text'] = $this->lang() == 'ar' ? 'تم طلب الاضافة' : 'requested to add';
                } elseif ($my_item->status == 'rejected') {
                    $arr['auction_status'] = 'تم رفض السلعة من قبل الادارة';
                    $arr['status_text'] = $this->lang() == 'ar' ? 'تم رفض السلعة من قبل الادارة' : 'rejected from admin';
                } elseif ($my_item->status == 'accepted') {
                    $arr['auction_status'] = 'بانتظار تسليم المركبة لساحة الحفظ';
                    $arr['status_text'] = $this->lang() == 'ar' ? 'بانتظار تسليم المركبة لساحة الحفظ' : 'waiting for deliver car to admin garage';
                } elseif ($my_item->status == 'delivered') {
                    $arr['auction_status'] = 'تم استلام المركبة من قبل الادارة';
                    $arr['status_text'] = $this->lang() == 'ar' ? 'تم استلام المركبة من قبل الادارة' : 'car delivered to admin garage';
                } else {
                    $arr['auction_status'] = 'تم جدولتها للمزاد';
                    $arr['status_text'] = $this->lang() == 'ar' ? 'تم جدولتها لمزاد' : 'prepared for auction';
                }
                $arr['negotiation'] = false;
                $arr['direct_pay'] = false;
                $arr['user_price'] = "";
                $arr['live'] = false;
                $arr['auction_type'] = $my_item->auction_type->name[$this->lang()];
                $arr['start_date'] = 123;
                $arr['auction_duration'] = 1;
                $arr['auction_price'] = 0;
            }
            $arr['id'] = (int)$my_item->id;
            $year = $my_item->year;
            $mark = $my_item->mark->name[$this->lang()];
            $model = $my_item->model->name[$this->lang()];
            if ($this->lang() == 'ar') {
                $name = sprintf(' %d - %s - %s ', $year, $mark, $model);
            } else {
                $name = sprintf(' %s - %s - %s ', $year, $mark, $model);
            }
            $arr['name'] = $name;
            $arr['item_status'] = $my_item->item_status->name[$this->lang()];
            $arr['city'] = $my_item->city->name[$this->lang()];
            $arr['image'] = $my_item->images[0];
            $arr['images'] = $my_item->images;
            $arr['is_favourite'] = $is_favourite;
            $arr['win'] = false;
            $arr["my_item"] = false;
            $data[] = $arr;
        }
        return $this->sendResponse($data);
    }

    public function userAuctions()
    {
        $user = auth()->user();
        $auction_users = AuctionUser::where('user_id', $user->id)->pluck('item_id');
        return $this->sendResponse(new ItemCollection(Item::whereIn('id', $auction_users)->latest()->get()));
    }

    public function myPaidItems()
    {
        $user = auth()->user();
        $paid_auction_items = AuctionItem::where('more_details->status', 'paid')->orWhere('more_details->status', 'delivered')->get();
        $item_ids = [];
        foreach ($paid_auction_items as $paid_auction_item) {
            $winner = AuctionUser::where(['auction_id' => $paid_auction_item->auction_id, 'item_id' => $paid_auction_item->item_id])->latest()->value('user_id');
            if ($winner == $user->id) {
                $item_ids[] = $paid_auction_item->item_id;
            }
        }
        return $this->sendResponse(new paidItemCollection(Item::whereIn('id', $item_ids)->latest()->get()));
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
        $paid_auction_items = AuctionItem::whereIn('item_id', $user_items_ids)->where(function ($query) {
            $query->where('more_details->status', 'paid')
                ->orWhere('more_details->status', 'delivered');
        })->get();
        $money = 0;
        foreach ($paid_auction_items as $paid_auction_item) {
            $money += $paid_auction_item->price;
        }
        foreach ($user->items() as $item) {
            $auction_item = AuctionItem::where('item_id', $item->id)->latest()->first();
            $now = Carbon::now();
            if ((Carbon::createFromTimestamp($auction_item->auction->more_details['end_date']) >= $now) && (Carbon::createFromTimestamp($auction_item->auction->start_date) <= $now)) {
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

//    public function canBid($id)
//    {
//        $user = User::find($id);
//        $response['profileCompleted'] = $user->profileAndPurchasingPowerIsFilled();
//        $user_purchasing_power = $user->purchasing_power;
//        $user_purchasing_power = $user_purchasing_power + $user->package->purchasing_power_increase;
//        $response['maxBid'] = (double)$user_purchasing_power * $this->purchasing_power_ratio;
//        return $this->sendResponse($response);
//    }

    public function favourite()
    {
        $item_ids = Favourite::where('user_id', \request()->user()->id)->pluck('item_id');
        $items = new ItemCollection(Item::whereIn('id', $item_ids)->latest()->get());
        return $this->sendResponse($items);
    }

    public function addBankAccount(Request $request)
    {
        $user = \request()->user();
        $user->update([
            'more_details' => [
                'bank' => [
                    'bank_name' => $request['bank_name'],
                    'iban_number' => $request['iban_number'],
                    'account_number' => $request['account_number'],
                ]
            ],
        ]);
        $data = new UserResource($user);
        return $this->sendResponse($data);
    }
}
