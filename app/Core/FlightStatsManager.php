<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 2.9.2016
 */

namespace App\Core;

use Carbon\Carbon;
use App\Alerts\Webhook\WebhookRepository;
use LArtie\FlightStatsApi\Core\Objects\ScheduledFlight;
use LArtie\FlightStatsApi\Core\Objects\StatusFlight;
use App\FlightNumber;
use App\User;
use LArtie\FlightStatsApi\Core\Methods\FlightStatus as FlightStatusApi;
use App\Repositories\FlightNumberRepository;
use App\Repositories\FlightStatusRepository;
use App\Repositories\UserRepository;

/**
 * Class FlightStatsManager
 * @package App\Core
 */
final class FlightStatsManager
{
    /**
     * @var ScheduledFlight
     */
    private $schedule;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Carbon
     */
    private $departureTime;

    /**
     * @var FlightNumber
     */
    private $flightNumber;

    /**
     * @var Carbon
     */
    private $arrivalTime;

    /**
     * FlightStatsManager constructor.
     *
     * @param ScheduledFlight $schedule
     * @param User $user
     * @param Carbon $departureTime
     * @param Carbon $arrivalTime
     */
    public function __construct(ScheduledFlight $schedule, User $user, Carbon $departureTime, Carbon $arrivalTime)
    {
        $this->schedule = $schedule;
        $this->user = $user;
        $this->departureTime = $departureTime;
        $this->arrivalTime = $arrivalTime;

        $this->flightNumber = FlightNumber::where([
            'carrier_code' => $this->schedule->carrierFsCode,
            'flight_number' => $this->schedule->flightNumber,
            'departure_time' => $this->schedule->departureTime,
            'arrival_time' => $this->schedule->arrivalTime
        ])->with('flightStatus')->first();
    }

    /**
     * Определяет дальнейшие действия.
     *
     * Если перелет уже имеется в БД, тогда закрепляет его за пользователем, иначе
     * осуществляет проверку можно ли извлечь данные о перелете, если нет, тогда
     * сохраняет все, что имеется на данный момент, иначе загружает статус рейса,
     * сохраняет его и закрепляет за пользователем
     */
    public function handle()
    {
        if ($this->flightNumber) {

            UserRepository::attachFlight($this->user, $this->flightNumber->flightStatus);

            return [
                'arrival_gate' => $this->flightNumber->flightStatus->arrival_gate,
                'departure_gate' => $this->flightNumber->flightStatus->departure_gate,
            ];
        } else {

            $data = [];

            if ($this->flightStatusIsAvailable()) {
                $data = $this->loadAndPrepareFlight();
            }
            $this->create($data);

            return [
                'arrival_gate' => $data['arrival_gate'] ?? null,
                'departure_gate' => $data['departure_gate'] ?? null
            ];
        }
    }

    /**
     * Загружает статус рейса и подготавливает данные для последующей их обработки
     *
     * @return array
     */
    public function loadAndPrepareFlight() : array
    {
        $data = [];

        $flightStatus = $this->loadFlights();

        if (isset($flightStatus[0])) {

            $flight = $flightStatus[0];

            $departureGate = $flight->getAirportResources()->departureGate ?? null;
            $arrivalGate = $flight->getAirportResources()->arrivalGate ?? null;

            $departureTerminal = $flight->getAirportResources()->departureTerminal ?? null;
            $arrivalTerminal = $flight->getAirportResources()->arrivalTerminal ?? null;

            $data = [
                'flight_id' => $flight->getFlightId(),
                'status' => $flight->getStatus(),
                'departure_gate' => $departureGate,
                'departure_terminal' => $departureTerminal,
                'arrival_gate' => $arrivalGate,
                'arrival_terminal' => $arrivalTerminal,
            ];
        }
        return $data;
    }

    /**
     * Сохраняет все совместные рейсы (code-share), устанавливает слежение за их изменениями и создает ассоциацию полета с пользователем
     *
     * @param array $flightInformation
     * @return bool
     */
    public function create(array $flightInformation = [])
    {
        $flightStatusRepository = new FlightStatusRepository();

        $flightData = [
            'departure_iata' => $this->schedule->getDepartureAirportFsCode(),
            'arrival_iata' => $this->schedule->getArrivalAirportFsCode(),
            'departure_date_local' => $this->departureTime->toDateTimeString(),
            'departure_date_utc' => $this->departureTime->setTimezone('UTC')->toDateTimeString(),
            'arrival_date_local' => $this->arrivalTime->toDateTimeString(),
            'arrival_date_utc' => $this->arrivalTime->setTimezone('UTC')->toDateTimeString(),
        ];

        $flightStatus = $flightStatusRepository->createIfNotExists(array_merge($flightInformation, $flightData));

        $flightNumberRepository = new FlightNumberRepository($flightStatus);

        $flightNumberRepository->createIfNotExists([
            'carrier_code' => $this->schedule->getCarrierFsCode(),
            'flight_number' => $this->schedule->getFlightNumber(),
            'departure_time' => $this->schedule->getDepartureTime(),
            'arrival_time' => $this->schedule->getArrivalTime(),
        ]);

        if ($this->schedule->getIsCodeshare()) {

            foreach ($this->schedule->getCodeshares() as $codeshare) {
                $flightNumberRepository->createIfNotExists([
                    'carrier_code' => $codeshare->carrierFsCode,
                    'flight_number' => $codeshare->flightNumber,
                    'departure_time' => $this->schedule->getDepartureTime(),
                    'arrival_time' => $this->schedule->getArrivalTime(),
                ]);
            }
        }

        if (WebhookRepository::isActive()) {
            $alertRule = WebhookRepository::createRule($this->schedule, $this->departureTime);

            $flightStatus->alerts()->create([
                'alert_id' => $alertRule->rule->id,
                'name' => $alertRule->rule->name,
                'description' => $alertRule->rule->description ?? null,
            ]);
        }

        UserRepository::attachFlight($this->user, $flightStatus);

        return true;
    }

    /**
     * Проверяет соответствует ли заданный авиаперелет условиям для текущй проверки или же требует отложенной
     *
     * @return bool
     */
    private function flightStatusIsAvailable() : bool
    {
        $availableDate = Carbon::now()->setTimezone($this->departureTime->getTimezone())->addDays(config('flyinghigh.flight_status.days'));

        return $availableDate->timestamp > $this->departureTime->timestamp;
    }

    /**
     * Загружка статуса рейса для выбранной даты
     *
     * @return StatusFlight[]
     */
    private function loadFlights() : array
    {
        $flightStatusApi = new FlightStatusApi();

        return $flightStatusApi->departingOnDate(
            $this->schedule->getCarrierFsCode(),
            $this->schedule->getFlightNumber(),
            $this->departureTime->year,
            $this->departureTime->month,
            $this->departureTime->day
        );
    }
}