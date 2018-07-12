<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class UpdateFlightsInfo
 * @package App\Console\Commands
 */
final class UpdateFlightsInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flights:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить инофрмацию об авиаперелётах';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
