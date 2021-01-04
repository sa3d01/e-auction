<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name'=>'user 1',
            'phone'=>'0552224788',
            'device->type'=>'IOS',
            'device->id'=>'IOS',
        ]);
        \App\User::create([
            'name'=>'user 2',
            'phone'=>'0552224799',
            'device->type'=>'ANDROID',
            'device->id'=>'ANDROID',
        ]);
    }
}
