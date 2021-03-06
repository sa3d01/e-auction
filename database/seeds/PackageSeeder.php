<?php

use App\Admin;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Package::create([
            'name->ar'=>'باقة مجانية',
            'name->en'=>'free package',
            'note->ar'=>[
                'السماح بالتصفح دون مميزات مدفوعة',
                'تخول المزايد لدخول ساحة الحفظ (المقرات التابعه للتطبيق)'
            ],
            'note->en'=>[
                'only view',
                'only view no pid'
            ],
        ]);
        \App\Package::create([
            'name->ar'=>'باقة ذهبية',
            'name->en'=>'golden package',
            'note->ar'=>[
                'السماح بالمزايدة بقيمة أعلى من القوة الشرائية بقيمة 1000 ريال',
                'السماح بالحصول على 3 ملفات فحص مدفوعة'
            ],
            'note->en'=>[
                'السماح بالمزايدة بقيمة أعلى من القوة الشرائية بقيمة 1000 ريال',
                'السماح بالحصول على 3 ملفات فحص مدفوعة'
            ],
            'price'=>1000,
            'purchasing_power_increase'=>1000,
            'paid_files_count'=>3,
        ]);
        \App\Package::create([
            'name->ar'=>'E-Auction',
            'name->en'=>'E-Auction',
            'note->ar'=>[
                'السماح بالمزايدة ',
                'السماح بالحصول على ملفات فحص '
            ],
            'note->en'=>[
                'السماح بالمزايدة ',
                'السماح بالحصول على ملفات فحص '
            ],
            'price'=>0,
            'purchasing_power_increase'=>0,
            'paid_files_count'=>0,
        ]);
    }
}
