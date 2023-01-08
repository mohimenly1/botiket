<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('gender_id')->references('id')->on('genders');
            $table->foreign('offer_id')->references('id')->on('offers');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories');
            $table->foreign('category_id')->references('id')->on('categories');
            
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
            $table->dropForeign('products_brand_id_foreign');
            $table->dropForeign('products_gender_id_foreign');
            $table->dropForeign('products_offer_id_foreign');
            $table->dropForeign('products_store_id_foreign');
            $table->dropForeign('products_sub_category_id_foreign');
            $table->dropForeign('products_category_id_foreign');
        });
    }
}
