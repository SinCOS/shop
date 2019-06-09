<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tixian extends Model
{
    protected $table = 'tixian';
    protected $fillable = ['status'];
   public const  STATUS_APPLIED = 0;
    const STATUS_FAIL = 2;
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function getbankinfoAttribute(){
        return unserialize($this->attributes['bankinfo']);
    }
}
