<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mockery\Exception;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable,ModelBaseFunctions;

    private $route='user';
    private $images_link='media/images/user/';

    protected $fillable = ['user_type_id','name','phone_verified_at','phone','phone_details','email','password','device','activation_code','status','online','image','location','more_details'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'phone_details' => 'json',
        'device' => 'json',
        'location' => 'json',
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

    protected function getPhoneAttribute()
    {
        return $this->phone_details['country_key'].$this->attributes['phone'];
    }

    public function nameForSelect(){
        return $this->name ;
    }
}
