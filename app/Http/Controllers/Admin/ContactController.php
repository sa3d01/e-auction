<?php

namespace App\Http\Controllers\Admin;

use App\Contact;
use App\Notification;
use App\User;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;

class ContactController extends MasterController
{
    public function __construct(Contact $model)
    {
        $this->model = $model;
        $this->route = 'contact';

        parent::__construct();
    }


    public function show_single_contact($id){
        if($id==0){
            $contact=Contact::latest()->first();
        }else{
            $contact=Contact::find($id);
        }
        if(!$contact)
            return response()->json(false);
        $contact->update(['read'=>'true']);
        if($contact->type=='claim'){
            $type='شكوى';
        }elseif($contact->type=='other'){
            $type='سبب آخر';
        }else{
            $type='اقتراح';
        }

        $route=route('admin.user.show',$contact->user->id);
        $message=$contact->message;
        $user_id=$contact->user->id;
        $name=$contact->user->name;
        $image=$contact->user->image;
        $published_at=$contact->published_from();
        $action = route('admin.contact.form', ['user_id' => $user_id,'contact_id' => $contact->id]);
        $div_message="<div class='message-head'>
                            <a class='btn btn-info btn-sm' data-href='$action' href='$action'><i class='os-icon os-icon-mail-07'></i><span>تواصل</span></a>
                            <div class='user-w with-status status-green'>
                                <div class='user-avatar-w'>
                                    <div class='user-avatar'>
                                       <img src='$image'>
                                    </div>
                                </div>
                                <div class='user-name'>
                                   <a href='$route'><h6 class='user-title'>$name</h6></a>
                                   <div class='user-role'>السبب <span>$type</span>
                                    </div>
                                </div>
                            </div>
                            <div class='message-info'>$published_at</div>
                            </div>
                            <div class='message-content'>$message</div>";

        $admin_replays_notifies=Notification::where(['receiver_id'=>$contact->user_id,'admin_notify_type'=>'single','type'=>'admin'])->where('more_details->contact_id',$contact->id)->get();
        $replies=[];
        foreach ($admin_replays_notifies as $admin_replays_notify){
            $arr['msg']=$admin_replays_notify->note['ar'];
            $arr['date']=$admin_replays_notify->published_from();
            $replies[]=$arr;
        }
        return response()->json(['div_message'=>$div_message,'replies'=>$replies]);
    }
    public function single_contact_form($user_id,$contact_id){
        return View('dashboard.contact.create', [
            'user_id' => $user_id,
            'contact_id' => $contact_id,
            'type'=>'contact',
            'action'=>'admin.contact.send',
            'title'=>'رسالة إدارية',
            'create_fields'=>[ 'النص' => 'note'],
        ]);
    }
    public function index()
    {
        $rows = $this->model->latest()->get();
        return View('dashboard.contact.index', [
            'rows' => $rows,
            'type'=>'contact',
            'title'=>'قائمة الرسائل',
            'index_fields'=>['الرسالة' => 'message'],
        ]);
    }
    public function show($id)
    {
        $row = $this->model->findOrFail($id);
        return View('dashboard.show.show', [
            'row' => $row,
            'type'=>'page',
            'action'=>'admin.page.update',
            'title'=>'تفاصيل',
            'edit_fields'=>['العنوان' => 'title', 'النص' => 'note'],
            'status'=>true,
            'languages'=>true,
            'image'=>true,
        ]);
    }
    public function send_single_notify($receiver_id,$note){
        $title['ar']='رسالة إدارية';
        $title['en']='admin message ';
        $note['ar']=$note;
        $note['en']=$note;
        $data=[];
        $data['title']=$title;
        $data['receiver_id']=$receiver_id;
        $data['note']=$note;
        $data['type']='admin';
        Notification::create($data);
//        $this->notify(User::find($receiver_id),$note);
        return true;
    }
    public function send_single_contact(Request $request){
        $title['ar']='رسالة إدارية';
        $title['en']='admin message ';
        $note['ar']=$request['note'];
        $note['en']=$request['note'];
        $data=[];
        $data['title']=$title;
        $data['receiver_id']=$request['user_id'];
        $data['note']=$note;
        $data['type']='admin';
        $data['admin_notify_type']='single';
        $data['more_details']['contact_id']=$request['contact_id'];
        Notification::create($data);
        $this->notify(User::find($request['user_id']),$request['note']);
        return redirect()->route('admin.contact.index')->with('created');
    }

    public function notify($receiver,$note){
        $push = new PushNotification('fcm');
        $msg = [
            'notification' => array('title'=>'رسالة إدارية', 'sound' => 'default'),
            'data' => [
                'title' => 'رسالة إدارية',
                'body' => $note,
                'status' => 'admin',
                'type'=>'admin',
            ],
            'priority' => 'high',
        ];
        $push->setMessage($msg)
            ->setDevicesToken($receiver->device['id'])
            ->send();
    }

}
