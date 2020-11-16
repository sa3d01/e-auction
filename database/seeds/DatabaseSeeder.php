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
             UserTypeSeeder::class,
             AdminSeeder::class,
             PackageSeeder::class,
             CategorySeeder::class,
             CarSeeder::class,
             PartnerSeeder::class,
             CitySeeder::class,
             SaleTypeSeeder::class,
             AskSeeder::class,
         ]);


    }
}
