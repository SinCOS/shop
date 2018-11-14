<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            // $table->string('description');
            $table->decimal('price', 10, 2)->comment('销售价');
            $table->decimal('cost',10,2)->comment('市场价或原价');
            $table->decimal('price_on_app',10,2)->comment('平台打款价');
            $table->unsignedInteger('stock')->comment('库存');
            $table->unsignedInteger('weight')->comment("重量单位g");
            $table->string("specs")->comment('规格项关联');
            $table->string('product_sn')->comment('商品条形码');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('product_skus');
    }
}
