<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityEx extends Model
{
    protected $table = 'activity_ex';
    protected $fillable = [
        'url','shop_id','activity_id'
    ];
    public function activity(){
        return $this->belongsTo(ActivityEx::class,'activity_id');
    }
}
