<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\FlightIdentifier;
use App\Jobs\GoogleCalendarSync;
use App\User;

/**
 * Class ReIdentificationFlights
 * @package App\Console\Commands
 */
final class ReIdentificationFlights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flights:re';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Повторное сканирование авиаперелетов';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with(['googleAccount' => function ($query) {
            $query->with(['calendar' => function ($query) {
                $query->with(['event' => function ($query) {
                    $now = Carbon::now()->addDays(5);

                    $query->where('identified', false);
                    $query->where('start', '<', $now);
                }]);
            }]);
        }])->get();

        foreach ($users as $user) {
            if (isset($user->googleAccount->calendar->event)) {
                $events = [];

                foreach ($user->googleAccount->calendar->event as $event) {
                    $events[] = $event;
                }

                $job = (new FlightIdentifier($events, $user, GoogleCalendarSync::RESPONSE_IF_IDENTIFIED))->onQueue('flight-identify');
                dispatch($job);
            }
        }
    }
}
