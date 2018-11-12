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
            $table->text('body');
            $table->unsignedInteger('max_buy')->default(0)->comment('单次最多购买');
            $table->decimal('dispatchprice',10,2)->comment('运费');
            $table->boolean('dispatchtype')->default(true)->comment('1 统一邮费 2 模板');
            $table->unsignedInteger('dispatchid')->default(0);
            $table->unsignedInteger('shop_id');
            $table->string('image');
            $table->boolean('on_sale')->default(true);
            $table->float('rating')->default(5);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->decimal('price', 10, 2);
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
       
        Schema::dropIfExists('products');
    }
}
