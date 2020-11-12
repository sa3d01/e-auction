<?php

use App\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name' => 'SuperAdmin',
            'email' => 'admin@admin.com',
            'password' => 'secret',
            'user_type_id' => 2,
        ]);
    }
}
