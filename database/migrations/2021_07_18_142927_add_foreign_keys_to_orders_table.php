<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('delivery_id')->references('id')->on('deliveries');
            $table->foreign('order_status_id')->references('id')->on('order_status');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_delivery_id_foreign');
            $table->dropForeign('orders_order_status_id_foreign');
            $table->dropForeign('orders_store_id_foreign');
            $table->dropForeign('orders_user_id_foreign');
        });
    }
}
