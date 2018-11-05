<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShopColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('shop', function (Blueprint $table) {
            $table->tinyInteger('type')->default(0)->comment('0 一般户 1 企业 2 自然人');
            $table->string('thumb')->default('')->comment('json 格式 sfzz 身份证正 sfzf 生份证反  yyzz 营业执照');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
