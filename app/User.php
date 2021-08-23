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
        'name','package_id','purchasing_power','phone','phone_details','email','licence_image','licence_number','more_details'
        ,'device','activation_code','status','image','phone_verified_at','email_verified_at','password','wallet','credit'
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
    public function items(){
        return $this->hasMany(Item::class);
    }
    public function nameForSelect(){
        return $this->name ;
    }
    public function profileIsFilled():bool{
        return $this->attributes['name']!=null && $this->attributes['package_id']!=null && $this->attributes['licence_image']!=null ;
    }
    public function profileAndPurchasingPowerIsFilled():bool{
        return $this->profileIsFilled() && $this->attributes['purchasing_power']!=0 ;
    }
    protected function getImageAttribute()
    {
        $dest=$this->images_link;
        try {
            if ($this->attributes['image'])
                return asset($dest). '/' . $this->attributes['image'];
            return asset('media/images/') . '/logo.jpeg';
        }catch (\Exception $e){
            return asset('media/images/') . '/logo.jpeg';
        }
    }
}
