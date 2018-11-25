<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
class sCategory extends Model
{
    use ModelTree, AdminBuilder;
    protected $table = 'sCategory';

    public function __construct(array $attributes = []){
    	$this->setParentColumn('parent_id');
        $this->setOrderColumn('order');
        $this->setTitleColumn('title');
    }
    public static function videoAll(){
    	return self::query()->where('parent_id','=',1)->orderBy('order')->latest()->pluck('title', 'id');
    }

    //
}
