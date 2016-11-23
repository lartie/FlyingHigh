<?php

namespace LArtie\Airports\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Class AirportInstall
 * @package LArtie\Airports\Console\Commands
 */
final class AirportInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airports:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install airports';

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
     */
    public function handle()
    {
        if ($this->confirm('Are you serious?')) {

            $sqlFile = File::get(__DIR__ . '/../../../database/airportsdump.sql');

            DB::connection('flyinghigh')->unprepared($sqlFile);
        }

        $this->line('Completed!');
    }
}
