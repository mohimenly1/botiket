<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->double('total_paid_amount')->default('0');
            $table->integer('order_id')->unsigned();
            $table->integer('coupon_id')->unsigned()->nullable();
            $table->enum('status', ['unpaid', 'partly paid', 'fully paid'])->default('unpaid');
            $table->double('discount')->nullable();
            $table->double('rest_amount');
            $table->integer('payment_method_id')->unsigned();
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
        Schema::dropIfExists('invoices');
    }
}
