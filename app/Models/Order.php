<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    protected $fillable = [
        'no', //订单
        'address', //地址json
        'total_amount',//数量
        'shop_id',
        'remark',//备注
        'paid_at',//支付时间
        'payment_method',
        'payment_no',//支付编号
        'refund_status',//退款状态
        'refund_no',//退款单号
        'closed',//关闭订单
        'reviewed',//
        'ship_status',//
        'ship_data',//购物信息
        'extra',//其他信
        'is_peisong'
        
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        // 'address'   => 'array',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class,'order_id','no');
    }

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class);
    }
    
    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
            usleep(100);
        }
        \Log::warning(sprintf('find order no failed'));

        return false;
    }
    public  static function getIndexStats(){
        $query = $this->query()->where('paid_at','>=',\Carbon\Carbon::today());
        $wait = $query->where('shop_status',self::SHIP_STATUS_PENDING)->count();
        $DELIVERED = $query->where('shop_status',self::SHIP_STATUS_DELIVERED)->count();
        return ['wait' => $wait,'delivered' =>$DELIVERED];
    }
    public static function getAvailableRefundNo()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
            // 为了避免重复我们在生成之后在数据库中查询看看是否已经存在相同的退款订单号
        } while (self::query()->where('refund_no', $no)->exists());

        return $no;
    }
   public function getAddressAttribute(){

       if($this->attributes['address'] == 'N;'){ return [];}
       return unserialize($this->attributes['address'])?:['zip' => ''];
   }
}
