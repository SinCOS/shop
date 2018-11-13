<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'body', 'image', 'on_sale', 'rating', 'sold_count', 'review_count', 'price','thumb','max_buy','dispathid','dispathprice'];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
         // 'thumb' => 'json'
    ];
    // 与商品SKU关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
    public function images(){
        return $this->hasMany(ProductImg::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function setThumbAttribute($val){
        if(is_array($val)){
            $this->attributes['thumb'] = json_encode($val);
        }
    }
    public function getThumbAttribute($image){
        // dd($image);
        return json_decode($image,true);
    }
   
    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
