<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatequantitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quantities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->string('size', 100);
            $table->integer('color_id')->unsigned();
            $table->integer('quantity');
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
        Schema::dropIfExists('quantities');
    }
}
