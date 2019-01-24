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
    const SHOP_STATUS_APPLY = 0 ;//申请
    const SHOP_STATUS_APPLY_FAIL = -1;//失败
    const SHOP_STATUS_NORMAL = 1;//正常
    const SHOP_STATUS_SUSPEED =2 ;//暂时休息 
    const SHOP_STATUS_CLOSED = -2 ;//关闭
    const SHOP_STATUS = [
        self::SHOP_STATUS_APPLY_FAIL =>'申请失败',
        self::SHOP_STATUS_APPLY => '申请开业',
        self::SHOP_STATUS_NORMAL => '正常营业',
        self::SHOP_STATUS_SUSPEED => '暂停营业',
        self::SHOP_STATUS_CLOSED => '店铺关闭'
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
        return $this->hasOne(User::class,'id','user_id');
    }
    public function category(){
        return $this->belongsTo(Category::class,'cat_id','id');
    }
    public function agent(){
        return $this->belongsTo(Agent::class, 'agent_id');
    }
    public function saller(){
        return $this->belongsTo(Agent::class, 'work_id');
    }
    public function setThumbAttribute($val){
        $array = json_decode($this->attributes['thumb'],true);
        if(isset($val['sfzz'])){
            $array['sfzz'] = $val['sfzz'];
        }
        if(isset($val['sfzf'])){
            $array['sfzf'] = $val['sfzf'];
        }
         if(isset($val['yyzz'])){
            $array['yyzz'] = $val['yyzz'];
        }
        if(isset($val['jyxkz'])){
            $array['jyxkz'] = $val['jyxkz'];
        }
             $this->attributes['thumb'] = json_encode($array);
       
    }
    public function setAreaAttribute($val){
        $this->attributes['area'] = json_encode($val);
    }
}
