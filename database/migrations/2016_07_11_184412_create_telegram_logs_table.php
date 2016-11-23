<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTelegramLogsTable
 */
final class CreateTelegramLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('telegram_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_id')->index();
            $table->string('to_id')->index();
            $table->text('message');
            $table->dateTime('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('flyinghigh')->dropIfExists('telegram_logs');
    }
}
