<?php

use Illuminate\Database\Seeder;
use \App\AuctionType;
class AuctionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AuctionType::create([
            'name->ar'=>'البيع لأعلى سعر',
            'name->en'=>'Sell for the highest price',
        ]);
        AuctionType::create([
            'name->ar'=>'البيع تحت موافقة البائع',
            'name->en'=>'Sale is under sellers approval',
        ]);
        AuctionType::create([
            'name->ar'=>'البيع لأقل سعر يقبل به البائع',
            'name->en'=>'Sell to the lowest price the seller will accept',
        ]);
        AuctionType::create([
            'name->ar'=>'البيع المباشر',
            'name->en'=>'direct sale',
        ]);
    }
}
