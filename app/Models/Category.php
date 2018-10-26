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
    public $timestamps =false;
    protected $fillable = ['title', 'icon', 'parent_id', 'description', 'thumb'];
     public function products()
    {
        return $this->hasMany(Product::class);
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
