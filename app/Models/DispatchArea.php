<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchArea extends Model
{
    protected $table = 'dispatchArea';
    protected $fillable = ['area','price'];
    public function getAreaAttribute($value)
    {
        return explode(',', $value);
    }

    public function setAreaAttribute($value)
    {
        $this->attributes['area'] = implode(',', $value);
    }



    public function dispatchv()
    {
        return $this->belongsTo(DispatchV::class, 'dispatchv_id');
    }
}
