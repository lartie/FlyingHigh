<?php

/**
 * Telegram API Bot routes
 */
Route::group(['prefix' => 'telegrambot', 'middleware' => 'web', 'as' => 'telegram'], function() {

    /**
     * Вебхук для Telegram
     */
    Route::post('webhook/' . config('telegrambot.token'), ['as' => '.webhook', 'uses' => 'LArtie\TelegramBot\Controllers\MainController@webhook']);
});
