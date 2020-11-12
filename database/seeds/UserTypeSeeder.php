<?php

use App\userType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        userType::create([
            'name' => 'user',
            'table' => 'users',
        ]);
        userType::create([
            'name' => 'superAdmin',
            'table' => 'admins',
            'status'=>0
        ]);

    }
}
