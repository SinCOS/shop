<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $table = 'dispatch';
    protected $fillable = [
        'name','startup_price','price','positions'
    ];
    // public function getPositionsAttribute($val){
    //     $arr = explode(',',$val);
    //     $tt = array_map(function($v) {
    //         return explode(' ',$v);
    //     },$arr);
    //     return $tt;
    // }
}
