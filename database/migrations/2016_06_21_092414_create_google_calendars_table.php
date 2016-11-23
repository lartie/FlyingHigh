<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateGoogleCalendarsTable
 */
final class CreateGoogleCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('google_calendars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('calendar_id', 171)->unique();
            $table->string('sync_token');
            $table->string('timezone');
            $table->integer('account_id')->unique()->unsigned()->index();
            $table->foreign('account_id')->references('id')->on('google_account')->onDelete('cascade');
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
        Schema::connection('flyinghigh')->dropIfExists('google_calendars');
    }
}
