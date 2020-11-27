<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Mockery\Exception;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable,ModelBaseFunctions;

    private $route='user';
    private $images_link='media/images/user/';

    protected $fillable = [
        'name','package_id','purchasing_power','phone','phone_details','email','licence_image','more_details'
        ,'device','activation_code','status','image','phone_verified_at','email_verified_at','password','wallet'
    ];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'phone_details' => 'json',
        'device' => 'json',
        'more_details' => 'json',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    //functions

    public function package(){
        return $this->belongsTo(Package::class);
    }
    public function nameForSelect(){
        return $this->name ;
    }
}
