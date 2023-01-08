<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->enum('action_types', ['update_order', 'update_brand', 'update_product','update_rate','create_offer']);
            $table->string('message');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions_history');
    }
}
