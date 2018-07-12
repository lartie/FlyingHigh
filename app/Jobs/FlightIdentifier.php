<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use LArtie\FlightStatsApi\Core\Exceptions\FlightStatsException;
use LArtie\FlightStatsApi\Core\Methods\Schedules;
use LArtie\FlightStatsApi\Core\Objects\ScheduledFlight;
use App\Core\FlightResponseManager;
use App\Core\FlightStatsManager;
use App\User;
use App\Repositories\AirportRepository;
use App\Repositories\EventRepository;
use LArtie\Google\Models\Event;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;

/**
 * Class FlightIdentifier
 * @package App\Jobs
 */
final class FlightIdentifier extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, BotLogsActivity;

    /**
     * @var array
     */
    private $events;

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $flag;

    /**
     * @var integer
     */
    const ALL_DATES_ARE_DIFFERENT = 0;

    /**
     * @var integer
     */
    const ALL_DATES_ARE_EQUAL = 1;

    /**
     * @var integer
     */
    const DEPARTURE_DATES_ARE_EQUAL = 2;

    /**
     * @var integer
     */
    const DEPARTURE_CITY_IS_NOT_EQUAL = 3;

    /**
     * Create a new job instance.
     *
     * @param Event[] $events
     * @param User $user
     * @param $flag
     */
    public function __construct(array $events, User $user, int $flag)
    {
        $this->events = $events;
        $this->user = $user;
        $this->flag = $flag;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Throwable
     * @throws TelegramSDKException
     */
    public function handle()
    {
        $flights = [];

        foreach ($this->events as $event) {
            $prepareEvent = EventRepository::getPrepareEventByEventID($event->id);

            if (isset($prepareEvent[0])) {
                $schedule = $this->identify($prepareEvent[0]);

                if (!empty($schedule)) {
                    $flights[] = $schedule;
                }
            }
        }
        $googleCalendarManager = new FlightResponseManager($this->user, $flights, $this->flag);
        $googleCalendarManager->handle();
    }

    /**
     * @param $event
     * @return array
     */
    private function identify($event)
    {
        $flight = [];

        try {
            $startEvent = Carbon::parse($event->start)->setTimezone($event->timezone);

            $schedulesApi = new Schedules();
            $schedules = $schedulesApi->departingFrom($event->iata_code, $startEvent->year, $startEvent->month, $startEvent->day, $startEvent->hour);

            $flight = $this->searchFlight($schedules, $event);

            if (!$flight instanceof ScheduledFlight) {

                if ($this->flag !== GoogleCalendarSync::RESPONSE_IF_IDENTIFIED) {
                    $flight = $event;
                }
            }
        } catch (FlightStatsException $e) {
            Log::critical('Error: ' . $e->getCode() . ' ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        }
        return $flight;
    }

    /**
     * @param ScheduledFlight[] $schedules
     * @param $event
     * @return array
     */
    private function searchFlight(array $schedules, $event)
    {
        $flight = [];

        foreach ($schedules as $schedule) {
            $dates = $this->getDates($schedule, $event);

            if (empty($dates)) {
                Log::info('Что-то не так с событием.' . PHP_EOL . var_export($event, true));
            } else {
                /*
                 * Является ли время рейса идентичным с временем события.
                 * Если да, то вероятно это необходимый перелёт.
                 */
                $datesInfo = $this->checkFlightData($schedule, $event, $dates);

                if ($datesInfo === FlightIdentifier::ALL_DATES_ARE_EQUAL) {
                    $flight = $this->saveFlight($schedule, $event->event_id, $dates);

                } else if ($datesInfo === FlightIdentifier::DEPARTURE_DATES_ARE_EQUAL) {
                    if (empty($flight)) {
                        $flight = $this->saveFlight($schedule, $event->event_id, $dates);
                    }
                } else if ($datesInfo === FlightIdentifier::DEPARTURE_CITY_IS_NOT_EQUAL) {
                    Log::info('Перелёт не соответствует необходимым требованиям.');
                    Log::info("Город прибытия не соответствует требованиям: $event->to $schedule->arrivalAirportFsCode");
                } else {
                    Log::info('Перелёт не соответствует необходимым требованиям.');
                    Log::info("Dep Iata: $schedule->departureAirportFsCode Dep time: $schedule->departureTime Arr Iata: $schedule->arrivalAirportFsCode Arr time: $schedule->arrivalTime");
                    Log::info("Dep Iata: $event->iata_code Dep time: $event->start Arr Iata: $event->to Arr time: $event->end");
                }
            }
        }
        return $flight;
    }

    /**
     * @param ScheduledFlight $schedule
     * @param object $event
     * @param array $dates
     * @return int
     */
    private function checkFlightData(ScheduledFlight $schedule, $event, array $dates) : int
    {
        $arrivalAirport = AirportRepository::getByIataCode($schedule->getArrivalAirportFsCode(), ['*'], true);

        if (str_contains($event->to, $arrivalAirport->city->name_en) || str_contains($event->to, $arrivalAirport->city->name_ru)) {

            if ($dates['departure_time']->toDateTimeString() === $dates['event_start']->toDateTimeString()) {

                if ($dates['arrival_time']->toDateTimeString() === $dates['event_end']->toDateTimeString()) {

                    return FlightIdentifier::ALL_DATES_ARE_EQUAL;
                }
                return FlightIdentifier::DEPARTURE_DATES_ARE_EQUAL;
            }
        } else {
            return FlightIdentifier::DEPARTURE_CITY_IS_NOT_EQUAL;
        }
        return FlightIdentifier::ALL_DATES_ARE_DIFFERENT;
    }

    /**
     * @param ScheduledFlight $schedule
     * @param int $eventId
     * @param array $dates
     * @return ScheduledFlight
     */
    private function saveFlight(ScheduledFlight $schedule, $eventId, array $dates) : ScheduledFlight
    {
        Log::info('Перелет найден! IATA: ' . $schedule->departureAirportFsCode . ' Dep:' . $dates['departure_time']);

        $flightStatsManager = new FlightStatsManager($schedule, $this->user, $dates['event_start'], $dates['event_end']);
        $gate = $flightStatsManager->handle();

        $schedule->{"departure_gate"} = $gate['departure_gate'];
        $schedule->{"arrival_gate"} = $gate['arrival_gate'];

        Event::where('event_id', $eventId)->update(['identified' => true]);

        return $schedule;
    }

    /**
     * Извлекает даты, подготавливает их для сравнения добавляя к ним корректную временную зону
     *
     * @param ScheduledFlight $schedule
     * @param object $event
     * @return Carbon[]
     */
    private function getDates(ScheduledFlight $schedule, $event) : array
    {
        $dates = [];

        $departureAirport = AirportRepository::getByIataCode($schedule->departureAirportFsCode);
        $arrivalAirport = AirportRepository::getByIataCode($schedule->arrivalAirportFsCode);

        if ($departureAirport && $arrivalAirport) {

            $dates = [
                'event_start' => Carbon::parse($event->start)->setTimezone($departureAirport->timezone),
                'event_end' => Carbon::parse($event->end)->setTimezone($arrivalAirport->timezone),
                'departure_time' => Carbon::parse($schedule->departureTime),
                'arrival_time' => Carbon::parse($schedule->arrivalTime)
            ];
        }
        return $dates;
    }
}
