<?php

namespace App\Http\Controllers\Admin;


use App\Setting;
use Illuminate\Http\Request;

class HomeController extends MasterController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        return view('dashboard.index');
    }
    public function setting(){
        $row = Setting::first();
        return View('dashboard.settings', [
            'row' => $row,
        ]);
    }
    public function update_setting(Request $request){
        $data=$request->all();

        $socials['twitter']=$request['twitter'];
        $socials['snap']=$request['snap'];
        $socials['instagram']=$request['instagram'];
        $data['socials']=$socials;

        $more_details['app_ratio']=$request['app_ratio'];
        $more_details['accept_offer_period']=$request['accept_offer_period'];
        $more_details['deliver_offer_period']=$request['deliver_offer_period'];
        $data['more_details']=$more_details;

        $about['user']=$request['about'];
        $data['about']=$about;

        $licence['user']=$request['licence_user'];
        $licence['provider']=$request['licence_provider'];
        $data['licence']=$licence;

        Setting::updateOrCreate(['id'=>1],$data);
        return redirect()->back()->with('updated', 'تم التعديل بنجاح');
    }

}
