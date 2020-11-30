<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('store');
            $table->unsignedInteger('category_id')->nullable();
            $table->string('title','200');
            $table->text('description');
            $table->string('cover')->nullable();
            $table->json('pictures')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('status');
            $table->string('sku_type','50')->nullable();

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
        Schema::dropIfExists('goods');
    }
}
