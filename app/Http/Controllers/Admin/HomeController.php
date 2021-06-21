<?php

namespace App\Http\Controllers\Admin;


use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $settings=Setting::first();
        $socials['twitter']=$request['twitter'];
        $socials['facebook']=$request['facebook'];
        $socials['snap']=$request['snap'];
        $socials['instagram']=$request['instagram'];
        $data['socials']=$socials;


        $about['ar']=$settings->about['ar'];
        $about['en']=$settings->about['en'];
        if ($request['about_ar']){
            $file=$request['about_ar'];
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move('media/files/', $filename);
            $about['ar'] = $filename;
        }
        if ($request['about_en']){
            $file=$request['about_en'];
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move('media/files/', $filename);
            $about['en'] = $filename;
        }
        $data['about']=$about;


        $licence['ar']=$settings->licence['ar'];
        $licence['en']=$settings->licence['en'];
        if ($request['licence_ar']){
            $file=$request['licence_ar'];
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move('media/files/', $filename);
            $licence['ar'] = $filename;
        }
        if ($request['licence_en']){
            $file=$request['licence_en'];
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move('media/files/', $filename);
            $licence['en'] = $filename;
        }
        $data['licence']=$licence;


        $privacy['ar']=$request['privacy_ar'];
        $privacy['en']=$request['privacy_en'];
        $data['privacy']=$privacy;

        $more_details['less_tenThousand']=$request['less_tenThousand'];
        $more_details['less_hundredThousand']=$request['less_hundredThousand'];
        $more_details['more_hundredThousand']=$request['more_hundredThousand'];
        $data['more_details']=$more_details;

        $contacts['email']=$request['email'];
        $contacts['mobile']=$request['mobile'];
        $contacts['address']=$request['address'];
        $contacts['lat']=$request['lat'];
        $contacts['lng']=$request['lng'];
        $data['contacts']=$contacts;

        Setting::updateOrCreate(['id'=>1],$data);
        return redirect()->back()->with('updated', 'تم التعديل بنجاح');
    }

}
