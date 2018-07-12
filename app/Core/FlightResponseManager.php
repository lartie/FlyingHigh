<?php

namespace App\Core;

use App\Jobs\GoogleCalendarSync;
use Carbon\Carbon;
use LArtie\FlightStatsApi\Core\Objects\ScheduledFlight;
use App\User;
use App\Repositories\AirportRepository;
use LArtie\TelegramBot\Core\FlightsManager;
use LArtie\TelegramBot\Repositories\UserRepository;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Throwable;

/**
 * Class FlightResponseManager
 * @package App\Core
 */
final class FlightResponseManager
{
    use BotLogsActivity;

    /**
     * @var User
     */
    private $user;

    /**
     * @var integer
     */
    private $flag;

    /**
     * @var Telegram
     */
    private $telegram;

    /**
     * @var ScheduledFlight[]
     */
    private $flights;

    /**
     * GoogleCalendarManager constructor.
     * @param User $user
     * @param array $flights
     * @param int $flag
     * @throws TelegramSDKException
     */
    public function __construct(User $user, array $flights, int $flag)
    {
        $this->telegram = new Telegram(config('telegrambot.token'));
        $this->user = $user;
        $this->flag = $flag;
        $this->flights = $flights;
    }

    /**
     * @return void
     * @throws Throwable
     * @throws TelegramSDKException
     */
    public function handle()
    {
        if (empty($this->flights)) {
            $attributes = $this->getAlertMessage();
        } else {
            $attributes = $this->getFlightsMessage();
        }

        if (isset($attributes['text'])) {

            $response = $this->telegram->sendMessage(array_merge([
                'chat_id' => $this->user->telegram_id,
                'parse_mode' => 'HTML',
            ], $attributes));

            $this->writeLog($response, $attributes['text']);

            $keyboard = [
                'keyboard' => [],
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ];

            if (UserRepository::flightsCount($this->user)) {
                $keyboard['keyboard'][] = ['✈️ My flights'];
            }

            $keyboard['keyboard'][] = ['⚒ Settings'];

            $response = $this->telegram->sendMessage([
                'chat_id' => $this->user->telegram_id,
                'text' => 'Choose:',
                'reply_markup' => Keyboard::make($keyboard)
            ]);
            $this->writeLog($response, 'Choose:');
        }
    }

    /**
     * @return array
     */
    private function getAlertMessage() : array
    {
        if ($this->flag !== GoogleCalendarSync::ALWAYS_RESPONSE) {
            return [];
        }

        return [
            'text' => trans('messages.flight.notFounded'),
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        [
                            'text' => trans('messages.support.contact'),
                            'url' => config('telegrambot.support')
                        ],
                    ],
                ]
            ]),
        ];
    }

    /**
     * @return array
     * @throws Throwable
     */
    private function getFlightsMessage() : array
    {
        $flights = [];

        if ($this->flag === GoogleCalendarSync::RESPONSE_IF_NOT_EMPTY) {
            foreach ($this->flights as $flight) {
                if ($flight instanceof ScheduledFlight) {
                    $flights[] = $this->getIdentifiedFlights($flight);
                } else {
                    $flights[] = $this->getUnidentifiedFlights($flight);
                }
            }
        }
        $flightsManager = new FlightsManager($this->user, 1);
        $flightsManager->setFlag($this->flag);

        $paginate = $flightsManager->paginate($flights);

        $response = view('telegram.flights')->with('flights', $paginate['items'])->with('flag', $this->flag)->render();

        return array_merge([
            'text' => $response,
        ], $paginate['keyboard']);
    }

    /**
     * @param ScheduledFlight $flight
     * @return array
     */
    private function getIdentifiedFlights(ScheduledFlight $flight) : array
    {
        $departureIata = $flight->getDepartureAirportFsCode();
        $departureAirport = AirportRepository::getByIataCode($departureIata, ['*'], true);
        $departureTime =  new Carbon($flight->getDepartureTime());

        $arrivalIata = $flight->getArrivalAirportFsCode();
        $arrivalAirport = AirportRepository::getByIataCode($arrivalIata, ['*'], true);
        $arrivalTime = new Carbon($flight->getArrivalTime());

        return [
            'identified' => true,
            'departure' => [
                'city' => $departureAirport->city->name_en,
                'iata' => $departureIata,
                'gate' => $flight->departure_gate,
                'terminal' => $flight->getDepartureTerminal(),
                'date' => $departureTime,
            ],
            'arrival' => [
                'city' => $arrivalAirport->city->name_en,
                'iata' => $arrivalIata,
                'gate' => $flight->arrival_gate,
                'terminal' => $flight->getArrivalTerminal(),
                'date' => $arrivalTime,
            ],
        ];
    }

    /**
     * @param object $event
     * @return array
     */
    private function getUnidentifiedFlights($event) : array
    {
        return [
            'identified' => false,
            'departure' => [
                'city' => $event->name_en,
                'date' => Carbon::parse($event->start)->setTimezone($event->start_timezone),
            ],
            'arrival' => [
                'city' => $event->to,
                'date' => Carbon::parse($event->end)->setTimezone($event->end_timezone),
            ],
        ];
    }
}
