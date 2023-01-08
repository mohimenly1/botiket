<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyUpdatesToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('OEM')->after('sku')->nullable();
            $table->string('url')->after('OEM')->nullable();
            $table->double('original_price')->after('price')->nullable();
            $table->double('discount_price')->after('original_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
