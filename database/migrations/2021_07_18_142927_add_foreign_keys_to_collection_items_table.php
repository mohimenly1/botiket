<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCollectionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collection_items', function (Blueprint $table) {
            $table->foreign('collection_id')->references('id')->on('collections');
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
        Schema::table('collection_items', function (Blueprint $table) {
            $table->dropForeign('collection_items_collection_id_foreign');
            $table->dropForeign('collection_items_product_id_foreign');
        });
    }
}
