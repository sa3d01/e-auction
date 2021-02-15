<?php

namespace App;

use App\Traits\ModelBaseFunctions;
use Illuminate\Database\Eloquent\Model;

class DropDown extends Model
{
    use ModelBaseFunctions;

    private $route='drop_down';
    private $images_link='media/images/drop_down/';

    protected $fillable = ['status','class','name','parent_id','image','more_details'];
    protected $casts = [
        'more_details' => 'json',
        'name' => 'json',
    ];
    public function nameForSelect(){
        return $this->name['ar'];
    }
    public function nameOfClass(){
        if ($this->attributes['class']=='Category'){
            return 'قسم';
        }elseif ($this->attributes['class']=='Mark'){
            return 'ماركة';
        }elseif ($this->attributes['class']=='Model'){
            return 'موديل';
        }elseif ($this->attributes['class']=='Partner'){
            return 'شركاء النجاح';
        }elseif ($this->attributes['class']=='City'){
            return 'المدن';
        }elseif ($this->attributes['class']=='itemStatus'){
            return 'حالات المركبات';
        }elseif ($this->attributes['class']=='ScanStatus'){
            return 'حالات الفحص';
        }elseif ($this->attributes['class']=='PaperStatus'){
            return 'حالات الاستمارة';
        }elseif ($this->attributes['class']=='Color'){
            return 'لون';
        }
    }
    public function parent(){
        return $this->belongsTo(DropDown::class,'parent_id','id');
    }
}
