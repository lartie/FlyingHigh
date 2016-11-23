<?php

namespace App\Alerts;

use Carbon\Carbon;
use App\FlightStatus;
use App\Repositories\AirportRepository;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;

/**
 * Class Response
 * @package App\Alerts
 */
final class Response
{
    /**
     * @var Telegram
     */
    private $telegram;

    /**
     * @var FlightStatus
     */
    private $flightStatus;

    /**
     * @var object
     */
    private $flightStatusUpdates;

    /**
     * WebhookResponse constructor.
     * @param FlightStatus $flightStatus
     * @param $flightStatusUpdates
     * @throws TelegramSDKException
     */
    public function __construct(FlightStatus $flightStatus, $flightStatusUpdates)
    {
        $this->telegram = new Telegram(config('telegrambot.token'));
        $this->flightStatus = $flightStatus;
        $this->flightStatusUpdates = $flightStatusUpdates;
    }

    /**
     * @param $event
     * @throws Throwable
     */
    public function handle($event)
    {
        $flight = $this->prepareFlights();

        $message = view($event['blade_template'])->with('flight', $flight)->render();

        $users = $this->flightStatus->users()->get();

        $keyboard = [
            'keyboard' => [
                ['✈️ My flights'],
                ['⚒ Settings']
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];

        foreach ($users as $user) {
            $this->telegram->sendMessage([
                'chat_id' => $user->telegram_id,
                'text' => $message,
                'reply_markup' => \GuzzleHttp\json_encode($keyboard),
            ]);
        }
    }

    /**
     * Выдает самую необходимую информацию по рейсу
     *
     * @return array
     */
    private function prepareFlights() : array
    {
        $departureAirport = AirportRepository::getByIataCode($this->flightStatus->departure_iata);
        $arrivalAirport = AirportRepository::getByIataCode($this->flightStatus->arrival_iata);

        $newDates = $this->getNewDates();

        $flight = [
            'departure' => [
                'city' => $departureAirport->city->name_en,
                'iata' => $departureAirport->iata_code,
                'gate' => $this->flightStatus->departure_gate,
                'terminal' => $this->flightStatus->departure_terminal,
                'date' => [
                    'new' => Carbon::parse($newDates['departureDateNew']) ?? $this->flightStatus->departure_date_local,
                    'old' => $this->flightStatus->departure_date_local,
                ],
            ],
            'arrival' => [
                'city' => $arrivalAirport->city->name_en,
                'iata' => $arrivalAirport->iata_code,
                'gate' => $this->flightStatus->arrival_gate,
                'terminal' => $this->flightStatus->arrival_terminal,
                'date' => [
                    'new' => Carbon::parse($newDates['arrivalDateNew']) ?? $this->flightStatus->arrival_date_local,
                    'old' => $this->flightStatus->arrival_date_local,
                ],
            ],
        ];
        return $flight;
    }

    /**
     * @return array
     */
    private function getNewDates() : array
    {
        $dates = [
            'departureDateNew' => null,
            'arrivalDateNew' => null,
        ];

        $updates = $this->flightStatusUpdates;

        if (isset($updates->flightStatusUpdate)) {
            foreach ($updates->flightStatusUpdate as $update) {
                if (isset($update->updatedDateFields)) {
                    if (isset($update->updatedDateFields->updatedDateField)) {
                        $dates = $this->getFields($update->updatedDateFields->updatedDateField, $dates);
                    }
                }
            }
        }
        return $dates;
    }

    /**
     * @param $fields
     * @param array $dates
     * @return array
     */
    private function getFields($fields, array $dates) : array
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if ($field->field === 'EGD') {
                    $dates['departureDateNew'] = $field->newDateLocal;
                } else if ($field->field === 'EGA') {
                    $dates['arrivalDateNew'] = $field->newDateLocal;
                }
            }
        } else if (is_object($fields)) {
            if ($fields->field === 'EGD') {
                $dates['departureDateNew'] = $fields->newDateLocal;
            } else if ($fields->field === 'EGA') {
                $dates['arrivalDateNew'] = $fields->newDateLocal;
            }
        }
        return $dates;
    }
}