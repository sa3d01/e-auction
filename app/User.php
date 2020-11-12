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

    protected $fillable = ['user_type_id','name','package_id','purchasing_power','phone_verified_at','email_verified_at','phone','phone_details','email','password','device','activation_code','status','image','licence_image','more_details'];
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

    //relations

    public function User_type(){
        return $this->belongsTo(userType::class);
    }

    //functions

    public function package(){
        return $this->belongsTo(Package::class);
    }
    public function nameForSelect(){
        return $this->name ;
    }
}
