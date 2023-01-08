<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->boolean('has_sales')->default('0');
            $table->boolean('is_store_of_the_week')->default('0');
            $table->boolean('is_featured')->default('0');
            $table->boolean('class_a_access');
            $table->boolean('is_active')->default('0');
            $table->timestamp('activated_at')->nullable();
            $table->integer('city_id')->unsigned();
            $table->string('phone', 13);
            $table->string('description');
            $table->string('longitude');
            $table->string('latitude');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
