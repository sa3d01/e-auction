<?php

use Illuminate\Database\Seeder;

class SaleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\SaleType::create([
            'name->ar'=>'البيع لأعلى سعر',
            'name->en'=>'Sell for the highest price',
        ]);
        \App\SaleType::create([
            'name->ar'=>'البيع تحت موافقة البائع',
            'name->en'=>'Sale is under sellers approval',
        ]);
        \App\SaleType::create([
            'name->ar'=>'البيع لأقل سعر يقبل به البائع',
            'name->en'=>'Sell to the lowest price the seller will accept',
        ]);
        \App\SaleType::create([
            'name->ar'=>'البيع المباشر',
            'name->en'=>'direct sale',
        ]);
    }
}
