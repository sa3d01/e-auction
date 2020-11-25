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
         $this->call([
             SettingSeeder::class,
             AdminSeeder::class,
             PackageSeeder::class,
             CategorySeeder::class,
             CarSeeder::class,
             PartnerSeeder::class,
             CitySeeder::class,
             AuctionTypeSeeder::class,
             AskSeeder::class,
         ]);


    }
}
