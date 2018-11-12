<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table ='video';

    protected $fillable = [
    	'title',
    	'category_id',
    	'is_hot',
    	'hits',
    	'user_id',
    	'link',
    	'thumb',
    	'status',
    ];
    const STATUS_CHECK = 0;
    const STATUS_NORMAL = 1;
    const STATUS_FAIL = -1;
    const STATUS_MAP =[
    	self::STATUS_CHECK => '待审核',
    	self::STATUS_FAIL => '失败',
    	self::STATUS_NORMAL => '通过',
    ];
    public function category(){
    	return $this->belongsTo(Category::class);
    }
    public function user(){
    	return $this->belongsTo(User::class);
    }

}
