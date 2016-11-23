<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFsEventsTable
 */
final class CreateFsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('fs_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->index();
            $table->string('value')->nullable()->default(null);
            $table->string('data_source');
            $table->date('datetime_recorded');
            $table->integer('alert_id')->unsigned()->index();
            $table->foreign('alert_id')->references('id')->on('fs_alerts')->onDelete('cascade');
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
        Schema::connection('flyinghigh')->dropIfExists('fs_events');
    }
}
