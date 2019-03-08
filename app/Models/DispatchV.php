<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchV extends Model
{
    //
    protected $table = 'dispatchV';


    public function getIncludeAttribute($value)
    {
        return explode(',', $value);
    }

    public function setIncludeAttribute($value)
    {
        $this->attributes['include'] = implode(',', $value);
    }
}
