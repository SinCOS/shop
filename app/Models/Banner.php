<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
	protected $table ='banner';
	protected $fillable = [
		'name','not_before','not_after','status','enabled'
	];
    
}
