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
        DropDown::create([
            'name->ar'=>'مركبات',
            'name->en'=>'Cars',
            'class'=>'Category',
        ]);
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

    }
}
