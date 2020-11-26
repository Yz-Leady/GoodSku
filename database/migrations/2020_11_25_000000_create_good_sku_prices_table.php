<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodSkuPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_sku_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('good_id');
            $table->unsignedInteger('good_sku_id');
            $table->json('prices')->nullable();
            $table->unsignedInteger('stock')->nullable();
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
        Schema::dropIfExists('good_sku_prices');
    }
}