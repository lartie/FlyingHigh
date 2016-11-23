<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUserFlightTable
 */
final class CreateUserFlightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('user_flight', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('flight_id')->unsigned()->index();
            $table->foreign('flight_id')->references('id')->on('fs_flight_status')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'flight_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('flyinghigh')->dropIfExists('user_flight');
    }
}
