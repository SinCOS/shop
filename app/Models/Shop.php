<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Shop extends Model
{
    const SHOP_USER_TYPE_NORMAL = 0 ;// 一般用户
    const SHOP_USER_TYPE_BUSINESS = 1; //企业用户
    const SHOP_USRR_TYPE_MAN = 2 ;//自然人
    const SHOP_USER_TYPE_MAP = [
        self::SHOP_USER_TYPE_BUSINESS => '企业用户',
        self::SHOP_USER_TYPE_NORMAL => '一般用户',
        self::SHOP_USRR_TYPE_MAN => '自然人'
    ];
    
    protected $table ='shop';
    protected $fillabled = [
        'title',
        'logo',
        'address',
        'area',
        'thumb',
        'money',
        'user_id',
        'cat_id',
        'agent_id', //市级代理
        'work_id', //业务员
        'background_image', 
        'serve_rating', //服务评分
        'concat_phone',
        'contcat_people', 
        'speed_rating' //速度评分
    ];
    protected $casts = [
        'area' => 'json',
        'thumb' => 'json'
    ];
//     public function getAreaAttribute($author)
// {
//     return json_decode($author, true);
// }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function category(){
        return $this->belongsTo(Category::class,'cat_id','id');
    }
    public function setThumbAttribute($val){
        $this->attributes['thumb'] = json_encode($val);
    }
    public function setAreaAttribute($val){
        $this->attributes['area'] = json_encode($val);
    }
}
