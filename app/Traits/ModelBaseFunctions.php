<?php

namespace App\Traits;

use App\Rating;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait ModelBaseFunctions
{
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    private function upload_file($file){
        $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($this->images_link, $filename);
        return $filename;
    }

    protected function setImageAttribute()
    {
        $image=request('image');
        $filename=null;
        if (is_file($image)) {
            $filename = $this->upload_file($image);
        }elseif (filter_var($image, FILTER_VALIDATE_URL) === True) {
            $filename = $image;
        }
        $this->attributes['image'] = $filename;
    }

    protected function getImageAttribute()
    {
        $dest=$this->images_link;
        try {
            if ($this->attributes['image'])
                return asset($dest). '/' . $this->attributes['image'];
            return asset($dest) . '/default.jpeg';
        }catch (\Exception $e){
            return asset($dest) . '/default.jpeg';
        }
    }
    protected function setLicenceImageAttribute()
    {
        $licence_image=request('licence_image');
        $filename=null;
        if (is_file($licence_image)) {
            $filename = $this->upload_file($licence_image);
        }elseif (filter_var($licence_image, FILTER_VALIDATE_URL) === True) {
            $filename = $licence_image;
        }
        $this->attributes['licence_image'] = $filename;
    }

    protected function getLicenceImageAttribute()
    {
        $dest=$this->images_link;
        try {
            if ($this->attributes['licence_image'])
                return asset($dest). '/' . $this->attributes['licence_image'];
            return "";
        }catch (\Exception $e){
            return "";
        }
    }

    protected function setPaperImageAttribute()
    {
        $paper_image=request('paper_image');
        $filename=null;
        if (is_file($paper_image)) {
            $filename = $this->upload_file($paper_image);
        }elseif (filter_var($paper_image, FILTER_VALIDATE_URL) === True) {
            $filename = $paper_image;
        }
        $this->attributes['paper_image'] = $filename;
    }

    protected function getPaperImageAttribute()
    {
        $dest=$this->images_link;
        try {
            if ($this->attributes['paper_image'])
                return asset($dest). '/' . $this->attributes['paper_image'];
            return "";
        }catch (\Exception $e){
            return "";
        }
    }

    protected function setPasswordAttribute($password)
    {
        if (isset($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function getStatusIcon()
    {
        if ($this->attributes['status'] ==='in_progress') {
            $name = 'جاري';
            $key = 'info';
        } elseif ($this->attributes['status'] ==='new') {
            $name = 'جديد';
            $key = 'warning';
        }elseif ($this->attributes['status'] ==='done') {
            $name = 'منتهى';
            $key = 'success';
        }elseif ($this->attributes['status'] === 1){
            $name = 'مفعل';
            $key = 'success';
        }else{
            $name = 'محظور';
            $key = 'danger';
        }
        return "<a class='badge badge-$key-inverted'>
                $name
                </a>";
    }


    public function activate()
    {
        $action = route('admin.'.$this->route.'.activate', ['id' => $this->attributes['id']]);
        if ($this->attributes['status'] === null || $this->attributes['status'] === 0) {
            $name = 'تفعيل';
            $key = 'success';
            $icon = 'check';
            $class = '';
        } else {
            $name = 'حظر';
            $key = 'danger';
            $icon = 'cancel';
            $class = 'block';
        }
        return "<a class='$class btn btn-$key btn-sm' data-href='$action' href='$action'><i class='os-icon os-icon-$icon-circle'></i><span>$name</span></a>";
    }

    public function published_from()
    {
        $created_at = Carbon::parse($this->attributes['created_at']);
        return $this->time($created_at->timestamp);
    }

    function time($timestamp, $num_times = 3)
    {
        $times = array(31536000 => 'سنة', 2592000 => 'شهر', 604800 => 'اسبوع', 86400 => 'يوم', 3600 => 'ساعة', 60 => 'دقيقة', 1 => 'ثانية');
        $now = time() - 3600;
        $timestamp -= 3600;
        $secs = $now - $timestamp;
        if ($secs == 0) {
            $secs = 1;
        }
        $count = 0;
        $time = '';
        foreach ($times as $key => $value) {
            if ($secs >= $key) {
                $s = '';
                $time .= floor($secs / $key);

                if ((floor($secs / $key) != 1)) $s = ' ';

                $time .= ' ' . $value . $s;
                $count++;
                $secs = $secs % $key;

                if ($count > $num_times - 1 || $secs == 0) break; else
                    $time .= ' ، ';
            }
        }
        $st = 'منذ ' . $time;
        return $st;
    }

    public function published_at()
    {
        return $this->ArabicDate($this->attributes['created_at']);
    }

    function ArabicDate($date)
    {
        $created_at = Carbon::parse($date);
        $months = array("Jan" => "يناير", "Feb" => "فبراير", "Mar" => "مارس", "Apr" => "أبريل", "May" => "مايو", "Jun" => "يونيو", "Jul" => "يوليو", "Aug" => "أغسطس", "Sep" => "سبتمبر", "Oct" => "أكتوبر", "Nov" => "نوفمبر", "Dec" => "ديسمبر");
        $your_date = $created_at->format('y-m-d');
        $en_month = date("M", strtotime($your_date));
        foreach ($months as $en => $ar) {
            if ($en == $en_month) {
                $ar_month = $ar;
            }
        }
        $find = array("Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri");
        $replace = array("السبت", "الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة");
        $ar_day_format = $created_at->format('D');
        $ar_day = str_replace($find, $replace, $ar_day_format);
        header('Content-Type: text/html; charset=utf-8');
        $standard = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $eastern_arabic_symbols = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
        $current_date = $ar_day . ' ' . $created_at->format('d') . ' / ' . $ar_month . ' / ' . $created_at->format('Y');
        return str_replace($standard, $eastern_arabic_symbols, $current_date);
    }


}
