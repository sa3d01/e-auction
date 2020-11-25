<?php

use Illuminate\Database\Seeder;
use \App\DropDown;
class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //marks
        //id=2
        DropDown::create([
            'name->ar'=>'Audi',
            'name->en'=>'Audi',
            'class'=>'Mark',
        ]);
        DropDown::create([
            'name->ar'=>'BMW',
            'name->en'=>'BMW',
            'class'=>'Mark',
        ]);
        DropDown::create([
            'name->ar'=>'Mercedes-Benz',
            'name->en'=>'Mercedes-Benz',
            'class'=>'Mark',
        ]);
        //models
        DropDown::create([
            'name->ar'=>'A3',
            'name->en'=>'A3',
            'parent_id'=>2,
            'class'=>'Model',
        ]);
        DropDown::create([
            'name->ar'=>'M5',
            'name->en'=>'M5',
            'class'=>'Model',
            'parent_id'=>3,
        ]);
        DropDown::create([
            'name->ar'=>'Sedan',
            'name->en'=>'Sedan',
            'class'=>'Model',
            'parent_id'=>4,
        ]);

        DropDown::create([
            'name->ar'=>'جديدة',
            'name->en'=>'new',
            'class'=>'ItemStatus',
        ]);
        DropDown::create([
            'name->ar'=>'كسر زيرو',
            'name->en'=>'almost new',
            'class'=>'ItemStatus',
        ]);
        DropDown::create([
            'name->ar'=>'يدوي',
            'name->en'=>'manual',
            'class'=>'Fetes',
        ]);
        DropDown::create([
            'name->ar'=>'أوتوماتيك',
            'name->en'=>'automatic',
            'class'=>'Fetes',
        ]);

        DropDown::create([
            'name->ar'=>'أسود',
            'name->en'=>'black',
            'class'=>'Color',
        ]);
        DropDown::create([
            'name->ar'=>'أبيض',
            'name->en'=>'white',
            'class'=>'Color',
        ]);
        DropDown::create([
            'name->ar'=>'أحمر',
            'name->en'=>'red',
            'class'=>'Color',
        ]);
        DropDown::create([
            'name->ar'=>'رمادى',
            'name->en'=>'gray',
            'class'=>'Color',
        ]);


        DropDown::create([
            'name->ar'=>'حديث',
            'name->en'=>'new',
            'class'=>'ScanStatus',
        ]);
        DropDown::create([
            'name->ar'=>'قديم',
            'name->en'=>'old',
            'class'=>'ScanStatus',
        ]);
        DropDown::create([
            'name->ar'=>'أكثر من 6 أشهر',
            'name->en'=>'more than 6 months',
            'class'=>'PaperStatus',
        ]);
        DropDown::create([
            'name->ar'=>'أقل من 6 أشهر',
            'name->en'=>'less than 6 months',
            'class'=>'PaperStatus',
        ]);

    }
}
