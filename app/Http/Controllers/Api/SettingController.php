<?php

namespace App\Http\Controllers\Api;

use App\Ask;
use App\Setting;
use function request;

class SettingController extends MasterController
{
    protected $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index()
    {
        $setting = Setting::first();
        $data = [];
        if (request()->header('lang') == 'en') {
            $data['about'] = $setting->about['en'];
            $data['licence'] = asset('media/files/'.$setting->licence['en']);
            $data['purchasing_power_text'] = $setting->purchasing_power_text['en'];
        } else {
            $data['about'] = $setting->about['ar'];
            $data['licence'] = asset('media/files/'.$setting->licence['ar']);
            $data['purchasing_power_text'] = $setting->purchasing_power_text['ar'];
        }
        $data['app_ratio'] = (integer)$setting->app_ratio;
        $data['tax_ratio'] = (integer)$setting->tax_ratio;
        $data['owner_tax_ratio'] = (integer)$setting->owner_tax_ratio;
        $data['finish_papers'] = (integer)$setting->finish_papers;
        $data['add_item_tax'] = (integer)$setting->add_item_tax;
        $data['socials'] = $setting->socials;
        $data['purchasing_power_ratio'] = (integer)$setting->purchasing_power_ratio;

        $data['less_tenThousand'] = (integer)$setting->more_details['less_tenThousand'];
        $data['less_hundredThousand'] = (integer)$setting->more_details['less_hundredThousand'];
        $data['more_hundredThousand'] = (integer)$setting->more_details['more_hundredThousand'];
        $data['bid_pause_period']=(integer)$setting->bid_pause_period;

        $data['email'] = $setting->contacts['email'];
        $data['mobile'] = $setting->contacts['mobile'];
        $data['address'] = $setting->contacts['address'];
        $data['lat'] = (double)$setting->contacts['lat'];
        $data['lng'] = (double)$setting->contacts['lng'];

        return $this->sendResponse($data);
    }

    public function asks()
    {
        $asks = Ask::all();
        $data = [];
        foreach ($asks as $ask) {
            if (request()->header('lang') == 'en') {
                $arr['ask'] = $ask->ask['en'];
                $arr['answer'] = $ask->answer['en'];
            } else {
                $arr['ask'] = $ask->ask['ar'];
                $arr['answer'] = $ask->answer['ar'];
            }
            $data[] = $arr;
        }
        return $this->sendResponse($data);
    }


}
