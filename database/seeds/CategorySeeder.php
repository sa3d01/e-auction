<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\DropDown::create([
            'name->ar'=>'مركبات',
            'name->en'=>'Cars',
            'class'=>'Category',
        ]);
    }
}
