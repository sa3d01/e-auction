<?php

use Illuminate\Database\Seeder;
use \App\DropDown;
class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DropDown::create([
            'class'=>'City',
            'name->ar'=>'الرياض',
            'name->en'=>'al-ryad',
        ]);
        DropDown::create([
            'class'=>'City',
            'name->ar'=>'جدة',
            'name->en'=>'gda',
        ]);
        DropDown::create([
            'class'=>'City',
            'name->ar'=>'الدمام',
            'name->en'=>'dmam',
        ]);
    }
}
