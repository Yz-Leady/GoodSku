<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsStatusLogsTable extends Migration
{

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('goods_status_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('goods_id')->index();
            $table->unsignedInteger('befor');
            $table->unsignedInteger('after');
            $table->text('remark')->nullable();
            $table->timestamp('created_at', 0);
            $table->index(['goods_id', 'befor', 'after']);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_status_logs');
    }

}