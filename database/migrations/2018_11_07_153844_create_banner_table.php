<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('活动名');
            $table->string('thumb')->comment('活动轮播图');
            $table->boolean('status')->default(true)->comment('活动状态');
            $table->datetime('not_before')->nullable()->comment('活动时间开始');
            $table->datetime('not_after')->nullable()->comment('活动结束');
            $table->string('province_id')->default('');
            $table->string('city_id')->default('');
            $table->string('district_id')->default('');
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
        Schema::dropIfExists('banner');
    }
}
