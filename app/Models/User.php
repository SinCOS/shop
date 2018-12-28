<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
     CONST MAN = 1;
    CONST WOMAN = 0;
    CONST SEXES = [
        self::MAN => '男',
        self::WOMAN => '女'
    ];
    
    CONST ACTIVE_STATUS = 1;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'mobile', 'password', 'email_verified','is_shop','shop_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
    ];
    public function shop(){
        return $this->hasOne(Shop::class);
    }
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public static function canAgents(){
        return self::query()->where('is_agent',0)->latest()->pluck('mobile', 'id');
    }
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products')
            ->withTimestamps()
            ->orderBy('user_favorite_products.created_at', 'desc');
    }
}
