<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToWishlistItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropForeign('wishlist_items_product_id_foreign');
            $table->dropForeign('wishlist_items_user_id_foreign');
        });
    }
}
