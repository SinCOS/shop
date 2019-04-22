<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';

    public function param(){
        return $this->hasMany(ActivityEx::class);
    }

    public function scopeAgent($query){
        $agent = \Admin::user()->agent;
        return $query->where('district_id',$agent->district_id);
    }
}
