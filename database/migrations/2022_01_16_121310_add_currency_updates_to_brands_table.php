<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyUpdatesToBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->float('increase_percentage')->after('logo')->nullable();
            $table->foreignId('original_currency_id')->nullable()->after('increase_percentage')->constrained('currencies')->cascadeOnDelete();
            $table->foreignId('selling_currency_id')->nullable()->after('original_currency_id')->constrained('currencies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('2021_07_18_142925_create_brands_table.php', function (Blueprint $table) {
            //
        });
    }
}
