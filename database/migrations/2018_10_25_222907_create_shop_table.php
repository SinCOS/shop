<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            
            $table->unsignedInteger('user_id');
             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('background_image',255);
            $table->tinyInteger('status')->comment('0 申请 1开通  -1注销');
            $table->string('concat_phone',32);
            $table->string('address',255);
            $table->string('contcat_people',32);
            $table->string('logo',255);
            $table->decimal('money',11,2);
            $table->string('note',255);
            $table->float('serve_rating')->default(5);
            $table->float('speed_rating')->default(5);
            $table->string('area');
            $table->unsignedInteger('agent_id')->comment('市级代理');
            $table->unsignedInteger('work_id')->comment('业务员');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop');
    }
}
