<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Percentages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('percentages', function (Blueprint $table) {
           
            $table->increments('id');
            $table->float('increase');
            
            $table->foreignIdFor(Brand::class);
            $table->foreignIdFor(Category::class);
            $table->foreignIdFor(SubCategory::class);

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
        //
    }
}
