<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_media', function (Blueprint $table) {
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
        Schema::table('product_media', function (Blueprint $table) {
            $table->dropForeign('product_media_product_id_foreign');
        });
    }
}
