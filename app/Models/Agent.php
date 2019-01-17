<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Agent extends Model
{
	use SoftDeletes;
    protected $fillable = [
        'user_id','param'
    ];
     protected $casts = [
       
        'param' => 'json'
        
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function setParamAttribute($val){
       $array = json_decode($this->attributes['param'],true);
        if(isset($val['sfzz'])){
            $array['sfzz'] = $val['sfzz'];
        }
        if(isset($val['sfzf'])){
            $array['sfzf'] = $val['sfzf'];
        }
         if(isset($val['yyzz'])){
            $array['yyzz'] = $val['yyzz'];
        }
        
             $this->attributes['param'] = json_encode($array);
        
       
    }

    protected static function boot(){
    	parent::boot();
    	static::deleting(function($agent){
            \App\Models\User::where('id',$agent->user_id)->update(['is_agent' => 0]);
			\DB::table('admin_role_users')->where('user_id', $agent->user_id)->delete();
    	});
    }

}
