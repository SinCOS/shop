<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table ='shop';
    protected $fillabled = [
        'title',
        'logo',
        'address',
        'area',
        'money',
        'agent_id', //市级代理
        'work_id', //业务员
        'background_image', 
        'serve_rating', //服务评分
        'concat_phone',
        'contcat_people', 
        'speed_rating' //速度评分
    ];
    // protected $casts = [
    //     'area' => 'json'
    // ];
    public function getAreaAttribute($author)
{
    return json_decode($author, true);
}
    public function setAreaAttribute($val){
        $this->attributes['area'] = json_encode($val);
    }
}
