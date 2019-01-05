<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Agent extends Model
{
	use SoftDeletes;
    protected $fillable = [
        'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    


    protected static function boot(){
    	parent::boot();
    	static::deleting(function($agent){
            \App\Models\User::where('id',$agent->user_id)->update(['is_agent' => 0]);
			\DB::table('admin_role_users')->where('user_id', $agent->user_id)->delete();
    	});
    }

}
