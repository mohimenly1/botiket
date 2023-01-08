<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToStoreCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_categories', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_categories', function (Blueprint $table) {
            $table->dropForeign('store_categories_category_id_foreign');
            $table->dropForeign('store_categories_store_id_foreign');
        });
    }
}
