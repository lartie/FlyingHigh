<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFsFlightNumbersTable
 */
final class CreateFsFlightNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('fs_flight_numbers', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('carrier_code')->index();
            $table->integer('flight_number')->unsigned()->index();
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('flight_id')->unsigned()->index();
            $table->foreign('flight_id')->references('id')->on('fs_flight_status')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['carrier_code', 'flight_number', 'departure_time', 'arrival_time'], 'flight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('flyinghigh')->dropIfExists('fs_flight_numbers');
    }
}
