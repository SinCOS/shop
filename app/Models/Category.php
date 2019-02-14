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
    protected $fillable = ['title', 'icon', 'parent_id', 'description', 'thumb','shop_id'];

    public function __construct(array $attributes = []){
    	$this->setParentColumn('parent_id');
        $this->setOrderColumn('order');
        $this->setTitleColumn('title');
    }
     public function products()
    {
        return $this->hasMany(Product::class);
    }
    public static function adminAll(){
    	return self::query()->where('shop_id',0)->orderBy('order')->pluck('title','id');
    }
    public static function selectOptions(\Closure $closure = null, $rootText = 'Root',$number = 0 )
    {
        $options = (new static())->withQuery($closure)->buildSelectOptions([],$number);

        return collect($options)->prepend($rootText, 0)->all();
    }

    public static function orderAll()
    {
        return self::query()->orderBy('order')->latest()->get();
    }
    // public static function selectedOptions()
    // {
    // 	$options = Category::where('parent_id',0)->orderby('order')->get()->toArray();
    // 	if (!empty($options))
    //     	$options = (new static())->buildSelectOptions($options);

    //     return collect($options)->prepend('Root',0)->all();
    // }
    public static function selectOrderAll()
    {
        return self::query()->orderBy('order')->latest()->pluck('title', 'id');
    }
}
