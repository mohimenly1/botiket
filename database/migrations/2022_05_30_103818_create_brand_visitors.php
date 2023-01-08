<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandVisitors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_visitors', function (Blueprint $table) {
            $table->id();
            $table->timestamp('end_date');
            $table->integer('brand_id')->unsigned();
            $table->integer('counter');
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_visitors');
    }
}
