<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $table = 'dispatch';
    protected $fillable = [
        'name','startup_price','price','positions'
    ];
    
}
