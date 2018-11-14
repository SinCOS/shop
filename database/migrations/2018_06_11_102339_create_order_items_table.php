<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('订单ID');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedInteger('product_id')->comment('产品ID');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('product_sku_id')->comment('规格ID');
            $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');
            $table->text('shop_info')->nullable()->cooment("根据需要保存sku 信息");
            $table->unsignedInteger('amount')->comment('订单详情');
            $table->decimal('price', 10, 2)->comment('销售价');
            $table->decimal('price_on_app',10,2)->comment('平台价格');
            $table->unsignedInteger('rating')->nullable()->comment('评分');
            $table->text('review')->nullable()->comment('评论');
            $table->timestamp('reviewed_at')->nullable()->comment('评论时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
