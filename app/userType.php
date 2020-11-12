<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class userType extends Model
{
    protected $fillable = ['name','status','table','parent_id'];
    protected $route='user_type';

    //relations

    public function users(){
        return $this->hasMany(User::class);
    }
    public function nameForSelect(){
        return $this->name == 'writer' ? 'شاعر' : 'استديو';
    }
}
