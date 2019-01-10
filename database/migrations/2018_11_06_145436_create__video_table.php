<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video', function (Blueprint $table) {
            $table->increments('id');
             $table->unsignedInteger('category_id');
            $table->foreign('category_id')->references('id')->on('scategory')->onDelete('cascade');
            $table->string('title');
            $table->string('link');
            $table->unsignedInteger('user_id');
            $table->boolean('status')->default('0')->comment('0 待审核，1pass');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_hot')->default(0)->comment('是否热榜');
            $table->unsignedInteger('hits')->default(0)->comment('点击量');
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
        Schema::dropIfExists('video');
    }
}
