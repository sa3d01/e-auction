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
            'purchasing_power_text->ar' => 'القوة الشرائية',
            'purchasing_power_text->en' => 'purchasing power nfo',
            'auction_period' => 90,
            'auction_increasing_period' => 10,
            'app_ratio' => 2,
            'purchasing_power_ratio' => 10,
            'tax_ratio' => 1,
            'socials->twitter'=>'https://',
            'socials->snap'=>'https://',
            'socials->instagram'=>'https://',
            'socials->facebook'=>'https://',
        ]);
    }
}
