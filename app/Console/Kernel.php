<?php

namespace App\Console;

use App\Console\Commands\GoogleRefreshChannels;
use App\Console\Commands\ReIdentificationFlights;
use App\Console\Commands\RootAdmin;
use App\Console\Commands\UpdateFlightsInfo;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * @package App\Console
 */
final class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RootAdmin::class,
        ReIdentificationFlights::class,
        UpdateFlightsInfo::class,
        GoogleRefreshChannels::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Обновление подписок на каналы пользователей.
         * @link https://developers.google.com/google-apps/calendar/v3/push#renewing-notification-channels
         */
        $schedule->command('google:rc')->daily();

        /**
         * Фоновая проверка авиаперелетов, которые не нашлись до.
         */
        $schedule->command('flights:re')->hourly();
    }
}
