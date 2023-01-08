<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToFavItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fav_items', function (Blueprint $table) {
            $table->foreign('fav_id')->references('id')->on('favorites');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fav_items', function (Blueprint $table) {
            $table->dropForeign('fav_items_fav_id_foreign');
            $table->dropForeign('fav_items_product_id_foreign');
        });
    }
}
