<?php

namespace App\Http\Controllers\Admin;

use App\AuctionItem;
use App\FeedBack;
use App\Http\Resources\ItemResource;
use App\Item;
use App\Notification;
use App\Report;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedBackController extends MasterController
{
    public function __construct(FeedBack $model)
    {
        $this->model = $model;
        $this->route = 'feed_back';
        parent::__construct();
    }

    public function index()
    {
        $rows = $this->model->latest()->get();
        return View('dashboard.feed_back.index', [
            'rows' => $rows,
            'type' => 'feed_back',
            'title' => 'قائمة آراء العملاء',
            'index_fields' => ['التعليق'=>'feed_back'],
            'selects' => [
                [
                    'name' => 'user',
                    'title' => 'المستخدم'
                ],
            ],
        ]);
    }

    function notify($feed_back, $note)
    {
        Notification::create([
            'receiver_id' => $feed_back->user_id,
            'title' => $note,
            'note' => $note,
        ]);
        $feed_back->user->device['type'] == 'IOS' ? $fcm_notification = array('title' => $note, 'sound' => 'default') : $fcm_notification = null;
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title' => $note, 'sound' => 'default'),
            'data' => [
                'title' => $note,
                'body' => $note,
                'type' => 'feed_back',
            ],
            'priority' => 'high',
        ];
        $fcm_feed_back=$push->setMessage($msg)
            ->setDevicesToken($feed_back->user->device['id'])
            ->send()
            ->getFeedback();
        return $fcm_feed_back;
    }

    public function activate($id, Request $request)
    {
        $feed_back = $this->model->find($id);
        $history[date('Y-m-d')]['approved'] = [
            'time' => date('H:i:s'),
            'admin_id' => Auth::user()->id,
        ];
        $feed_back->update([
            'status' => 'approved',
            'more_details' => [
                'history' => $history,
            ],
        ]);
        $note['ar'] = 'تم قبول اضافة رأيك بالتطبيق من قبل الادارة ..';
        $note['en'] = 'your added feed back is accepted from admin  ..';
        $fcm_feed_back=$this->notify($feed_back, $note);
        print_r( $fcm_feed_back);
        return ;
        $feed_back->refresh();
        return redirect()->back()->with('updated');
    }
}
