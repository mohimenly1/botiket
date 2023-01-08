<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->double('delivery_price')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('store_id')->unsigned()->nullable();
            $table->integer('order_status_id')->unsigned()->default(1);
            $table->double('total_amount')->nullable();
            $table->date('delivery_date')->nullable();
            $table->integer('delivery_id')->unsigned()->nullable();
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
        Schema::dropIfExists('orders');
    }
}
