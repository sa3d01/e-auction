<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'phone_details->country_key' =>'+966',
        'phone_details->country_name' =>'المملكة العربية السعودية',
        'phone_details->country_flag' =>'https://restcountries.eu/data/sau.svg',
        'phone' => '5'.rand(11111111,99999999),
        'email' => $faker->unique()->safeEmail,
        'phone_verified_at' => now(),
        'password' => 'secret',
        'remember_token' => Str::random(10),
        'status' => $faker->randomElement([0,1]),
        'online' => $faker->randomElement([0,1]),
        'user_type_id' => 1,
        'image' => $faker->randomElement(['0WhjhQRcSG.jpeg', '1bUjhnpa5v.jpg','3JcGxWK1Pe.jpeg','9WS22UZ01K.jpg','15AEL6tLCp.jpg']),
        'device->type'=>$faker->randomElement(['IOS','ANDROID']),
        'device->id'=>$faker->randomElement(['IOS','ANDROID']),
    ];
});
