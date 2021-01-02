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
        $socials['facebook']=$request['facebook'];
        $socials['snap']=$request['snap'];
        $socials['instagram']=$request['instagram'];
        $data['socials']=$socials;


        $about['ar']=$request['about_ar'];
        $about['en']=$request['about_en'];
        $data['about']=$about;

        $licence['ar']=$request['licence_ar'];
        $licence['en']=$request['licence_en'];
        $data['licence']=$licence;

        Setting::updateOrCreate(['id'=>1],$data);
        return redirect()->back()->with('updated', 'تم التعديل بنجاح');
    }

}
