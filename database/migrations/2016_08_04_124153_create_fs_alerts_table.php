<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFsAlertsTable
 */
final class CreateFsAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('fs_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('alert_id')->unique()->unsigned()->index();
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->integer('flight_id')->unsigned()->index();
            $table->foreign('flight_id')->references('id')->on('fs_flight_status')->onDelete('cascade');
            $table->boolean('active')->default(true);
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
        Schema::connection('flyinghigh')->dropIfExists('fs_alerts');
    }
}
