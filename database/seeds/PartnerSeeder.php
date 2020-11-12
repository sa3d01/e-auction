<?php

use Illuminate\Database\Seeder;
use App\DropDown;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DropDown::create([
           'class'=>'Partner',
           'name->ar'=>'سعد سالم',
           'name->en'=>'saad salem',
        ]);
        DropDown::create([
           'class'=>'Partner',
           'name->ar'=>'عمر',
           'name->en'=>'Omar',
        ]);
    }
}
