<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoodsConfigsTable extends Migration
{

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('goods_configs', function (Blueprint $table) {
            $table->string('showtype', 20)->nullable();
            $table->boolean('stock_type')->default(0)->comment('扣库存方式');
            $table->boolean('is_push')->default(0)->comment('是否推荐');
            $table->unsignedInteger('push_order')->default(0)->comment('推荐排序');
            $table->boolean('is_preferential')->default(0)->comment('是否特卖');
            $table->timestamp('preferential_start')->nullable();
            $table->timestamp('preferential_end')->nullable();
            $table->unsignedInteger('preferential_rate')->default(0);
            $table->boolean('is_limit')->default(0)->comment('是否限购');
            $table->unsignedInteger('limit_number')->default(0)->comment('限购数量');
            $table->boolean('freight_type')->default(0)->comment('运费模式');
            $table->boolean('freight_single')->default(0)->comment('单件运费金额');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('goods_configs', function (Blueprint $table) {
            //
        });
    }

}
