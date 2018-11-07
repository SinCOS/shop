<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    use ModelTree, AdminBuilder;
    protected $table = 'categories';
    public $timestamps =true;
    protected $fillable = ['title', 'icon', 'parent_id', 'description', 'thumb',];

    public function __construct(array $attributes = []){
    	$this->setParentColumn('parent_id');
        $this->setOrderColumn('order');
        $this->setTitleColumn('title');
    }
     public function products()
    {
        return $this->hasMany(Product::class);
    }
    public static function videoAll(){
    	return self::query()->where('parent_id','=',10)->orderBy('order')->latest()->pluck('title', 'id');
    }
    public static function adminAll(){
    	return self::query()->where('uid','=',0)->orderBy('order')->latest()->get();
    }

    public static function orderAll()
    {
        return self::query()->orderBy('order')->latest()->get();
    }

    public static function selectOrderAll()
    {
        return self::query()->orderBy('order')->latest()->pluck('title', 'id');
    }
}
