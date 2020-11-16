<?php

use Illuminate\Database\Seeder;
use \App\DropDown;
class CategorySeeder extends Seeder
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
    }
}
