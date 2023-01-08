<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToStoreGendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_genders', function (Blueprint $table) {
            $table->foreign('gender_id')->references('id')->on('genders');
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
        Schema::table('store_genders', function (Blueprint $table) {
            $table->dropForeign('store_genders_gender_id_foreign');
            $table->dropForeign('store_genders_store_id_foreign');
        });
    }
}
