<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_coupon_id_foreign');
            $table->dropForeign('invoices_order_id_foreign');
            $table->dropForeign('invoices_payment_method_id_foreign');
        });
    }
}
