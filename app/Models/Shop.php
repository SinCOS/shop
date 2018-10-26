<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class shop extends Model
{
    protected $table ='shop';
    protected $fillabled = [
        'title',
        'logo',
        'address',
        'area'
    ];
}
