<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

namespace LArtie\TelegramBot;

use Illuminate\Support\ServiceProvider;
use LArtie\TelegramBot\Controllers\MainController;

/**
 * Class TelegramBotServiceProvider
 * @package LArtie\TelegramBot
 */
final class TelegramBotServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/telegrambot.php' => config_path('telegrambot.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        require __DIR__ . '/../routes.php';

        $this->app->make(MainController::class);
    }
}
