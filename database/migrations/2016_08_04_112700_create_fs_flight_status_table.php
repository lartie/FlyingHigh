<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFsFlightStatusTable
 */
final class CreateFsFlightStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('fs_flight_status', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('flight_id')->unique()->unsigned()->nullable();
            $table->char('departure_iata', 3);
            $table->char('arrival_iata', 3);
            $table->char('status', 2);
            $table->char('departure_gate')->nullable()->default(null);
            $table->char('departure_terminal')->nullable()->default(null);
            $table->dateTimeTz('departure_date_local');
            $table->dateTimeTz('departure_date_utc');
            $table->char('arrival_gate')->nullable()->default(null);
            $table->char('arrival_terminal')->nullable()->default(null);
            $table->dateTimeTz('arrival_date_local');
            $table->dateTimeTz('arrival_date_utc');
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
        Schema::connection('flyinghigh')->dropIfExists('fs_flight_status');
    }
}
