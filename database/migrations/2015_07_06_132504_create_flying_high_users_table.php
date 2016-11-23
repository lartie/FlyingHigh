<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFlyingHighUsersTable
 */
final class CreateFlyingHighUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('flyinghigh')->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('telegram_id')->unique()->unsigned()->index();
            $table->string('username')->default(null)->nullable();
            $table->string('first_name')->default(null)->nullable();
            $table->string('last_name')->default(null)->nullable();
            $table->json('settings');
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
        Schema::connection('flyinghigh')->dropIfExists('users');
    }
}
