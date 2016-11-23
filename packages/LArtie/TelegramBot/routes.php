<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

/**
 * Telegram API Bot routes
 */
Route::group(['prefix' => 'telegrambot', 'middleware' => 'web', 'as' => 'telegram'], function() {

    /**
     * Вебхук для Telegram
     */
    Route::post('webhook/' . config('telegrambot.token'), ['as' => '.webhook', 'uses' => 'LArtie\TelegramBot\Controllers\MainController@webhook']);
});