<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateGoogleEventsTable
 */
final class CreateGoogleEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('google_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event_id', 500);
            $table->string('from');
            $table->string('to');
            $table->dateTime('start');
            $table->string('start_timezone');
            $table->dateTime('end');
            $table->string('end_timezone');
            $table->integer('calendar_id')->unsigned()->index();
            $table->foreign('calendar_id')->references('id')->on('google_calendars')->onDelete('cascade');
            $table->boolean('identified')->default(false);
            $table->unique(['calendar_id', 'event_id'], 'event');
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
        Schema::connection('flyinghigh')->dropIfExists('google_events');
    }
}
