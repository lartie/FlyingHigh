<?php

namespace LArtie\Airports;

use Illuminate\Support\ServiceProvider;
use LArtie\Airports\Console\Commands\AirportInstall;

/**
 * Class AirportsServiceProvider
 * @package LArtie\Airports
 */
final class AirportsServiceProvider extends ServiceProvider
{
    /**
     * List of artisan commands
     * @var array
     */
    protected $commands = [
        AirportInstall::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
