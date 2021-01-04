<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //settings
        \App\Setting::create([
            'about->ar' => 'عن التطبيق',
            'about->en' => 'about',
            'licence->ar' => 'الشروط والأحكام',
            'licence->en' => 'licence',
            'socials->twitter'=>'https://',
            'socials->snap'=>'https://',
            'socials->instagram'=>'https://',
            'socials->facebook'=>'https://',
            'purchasing_power_text->ar' => 'القوة الشرائية',
            'purchasing_power_text->en' => 'purchasing power nfo',
            'purchasing_power_ratio' => 10,//percent
            'auction_increasing_period' => 10,//second
            'app_ratio' => 2,//percent
            'add_item_tax' => 100,//fixed amount
            'tax_ratio' => 1,//percent
            'auction_period' => 90,//second
        ]);
    }
}
