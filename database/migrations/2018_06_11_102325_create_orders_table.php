<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->unique()->comment('订单编号');
            $table->unsignedInteger('user_id')->comment('用户');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('address')->comment("地址信息");
            $table->unsignedInteger('shop_id')->comment('店铺ID');
            $table->decimal('total_amount')->comment("数量");
            $table->text('remark')->nullable()->comment('备注');
            $table->dateTime('paid_at')->nullable()->comment("支付时间");
            $table->string('payment_method')->nullable()->comment("支付方式");
            $table->string('payment_no')->nullable()->comment('支付订单号');
            $table->string('refund_status')->default(\App\Models\Order::REFUND_STATUS_PENDING)->comment('退款状态');
            $table->string('refund_no')->nullable()->comment('退款编号');
            $table->boolean('closed')->default(false)->comment('订单关闭');
            $table->boolean('reviewed')->default(false);
            $table->string('ship_status')->default(\App\Models\Order::SHIP_STATUS_PENDING)->comment("订单发货状态");
            $table->text('ship_data')->nullable()->comment('订单发货信息json');
            $table->text('extra')->nullable()->comment('其他信息');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
