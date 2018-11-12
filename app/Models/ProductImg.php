<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductImg extends Model
{
	protected $table = 'product_img';
    protected $fillable = ['img'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
