<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //users
         $this->call([
             UserTypeSeeder::class,
             UserSeeder::class,
             AdminSeeder::class,
         ]);
         //settings
        \App\Setting::create([
            'about->ar' => 'عن التطبيق',
            'licence->ar' => 'الشروط والأحكام',
            'private->ar' => 'الشروط والأحكام',
            'socials->twitter'=>'https://',
            'socials->snap'=>'https://',
            'socials->instagram'=>'https://',
        ]);
        //contact types
        \App\DropDown::create([
            'class' => 'Contact',
            'name->ar' => 'اقتراح'
        ]);
        \App\DropDown::create([
            'class' => 'Contact',
            'name->ar' => 'شكوى'
        ]);
        \App\DropDown::create([
            'class' => 'Contact',
            'name->ar' => 'غير ذلك'
        ]);
    }
}
