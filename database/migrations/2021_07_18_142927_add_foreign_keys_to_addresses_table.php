<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('city_id')->references('id')->on('cities');
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
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign('addresses_city_id_foreign');
            $table->dropForeign('addresses_user_id_foreign');
        });
    }
}
