<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToFollowedStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('followed_stores', function (Blueprint $table) {
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
        Schema::table('followed_stores', function (Blueprint $table) {
            $table->dropForeign('followed_stores_store_id_foreign');
            $table->dropForeign('followed_stores_user_id_foreign');
        });
    }
}
