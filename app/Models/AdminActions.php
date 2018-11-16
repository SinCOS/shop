<?php

namespace App\Models;

trait AdminActions
{
    public  static function Own(){
        return (new static)->where('shop_id',\Admin::user()->shop_id);
    }
}
