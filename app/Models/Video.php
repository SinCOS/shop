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
    public function category(){
    	return $this->belongsTo(Category::class);
    }
    public function user(){
    	return $this->belongsTo(User::class);
    }

}
