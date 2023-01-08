<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->unsigned()->nullable();
            $table->string('sku');
            $table->string('title');
            $table->boolean("is_new")->nullable();
            $table->text('description');
            $table->double('price');
            $table->boolean('is_shipped')->default(0);
            $table->boolean('is_featured');
            $table->integer('brand_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned();
            $table->integer('sub_category_id')->unsigned();
            $table->integer('offer_id')->unsigned()->nullable();
            $table->integer('gender_id')->unsigned();
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
        Schema::dropIfExists('products');
    }
}
