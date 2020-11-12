<?php

namespace App;
use App\Traits\ModelBaseFunctions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable,ModelBaseFunctions,HasRoles;
    private $route='admin';
    private $images_link='media/images/user/';
    protected $guard = 'admin';
    protected $fillable = ['name','email','mobile','image','password','activation_code','activation_status','status','user_type_id'];
    protected $hidden = ['password', 'remember_token'];

    public function getAllPermissionsAttribute()
    {
        $res = [];
        $allPermissions = $this->getAllPermissions();
        foreach ($allPermissions as $p) {
            $res[] = $p->name;
        }
        return $res;
    }
    public function User_type(){
        return $this->belongsTo(userType::class);
    }
    public function getRoleArabicName()
    {
        $role=$this->roles()->first();
        return $role->blank ?? '';
    }
}
