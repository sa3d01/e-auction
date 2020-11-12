<?php

namespace App\Console\Commands;

use App\Http\Resources\OrderResource;
use App\Notification;
use App\Order;
use App\Setting;
use Carbon\Carbon;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Console\Command;

class CancelOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cancel pined orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deliver_offer_period=Setting::value('more_details->deliver_offer_period');
        $accept_offer_period=Setting::value('more_details->accept_offer_period');
        $now=Carbon::now()->addHours(2);
        foreach (Order::where('status','new')->get() as $order){
            if ($order->price===0 && $now->diffInMinutes(Carbon::parse($order->created_at)) > $deliver_offer_period){
                $this->notify($order, $order->provider, $order->user, 'تم رفض الطلب رقم #' . $order->id . ' لعدم استقبال عرض');
                $this->notify($order, $order->user, $order->provider, 'تم رفض الطلب رقم #' . $order->id . ' لعدم ارسال عرض');
                $order->update([
                    'status'=>'done',
                    'cancel_reason'=>'تم رفض الطلب رقم #' . $order->id . ' لعدم استقبال عرض'
                ]);
            }
            if ($order->price!=0 && $now->diffInMinutes(Carbon::parse($order->created_at)) > $accept_offer_period){
                $this->notify($order, $order->provider, $order->user, 'تم رفض الطلب رقم #' . $order->id . ' لعدم قبول العرض');
                $this->notify($order, $order->user, $order->provider, 'تم رفض الطلب رقم #' . $order->id . ' لعدم قبول العرض');
                $order->update([
                    'status'=>'done',
                    'cancel_reason'=>'تم رفض الطلب رقم #' . $order->id . ' لعدم قبول العرض'
                ]);
            }
        }
        return true;
    }
    public function notify($order,$sender,$receiver,$title){
        $receiver->device['type'] =='IOS'? $fcm_notification=array('title'=>$title, 'sound' => 'default') : $fcm_notification=null;
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => $fcm_notification,
            'data' => [
                'title' => $title,
                'body' => $title,
                'status' => $order->status,
                'type'=>'order',
                'order'=>new OrderResource($order)
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($receiver->device['id'])
            ->send();
        $notification=new Notification();
        $notification->type='app';
        $notification->sender_id=$sender->id;
        $notification->receiver_id=$receiver->id;
        $notification->order_id=$order->id;
        $notification->title=$title;
        $notification->note=$title;
        $notification->save();
    }
}
