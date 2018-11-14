<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('title');
            $table->text('body')->comment('详情');
            $table->unsignedInteger('max_buy')->default(0)->comment('单次最多购买');
            $table->decimal('dispatchprice',10,2)->comment('运费');
            $table->boolean('dispatchtype')->default(true)->comment('1 统一邮费 2 模板');
            $table->unsignedInteger('dispatchid')->default(0)->comment('运费模板');
            $table->unsignedInteger('shop_id')->comment('店铺id');
            $table->string('image')->comment('主图');
            $table->boolean('on_sale')->default(true)->comment('是否上架');
            $table->float('rating')->default(5)->comment('评分');
            $table->unsignedInteger('sold_count')->default(0)->comment('销售数量');
            $table->unsignedInteger('review_count')->default(0)->comment('评论数量');
            $table->decimal('price', 10, 2)->comment('价格');
            $table->decimal('price_on_app',10,2)->comment('平台价格');
            $table->json('thumb')->nullable()->comment('副图');
            $table->boolean('has_sku')->default(0)->comment('是否有规格');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
        Schema::dropIfExists('products');
    }
}
