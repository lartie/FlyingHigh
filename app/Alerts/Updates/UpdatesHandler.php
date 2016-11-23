<?php

namespace App\Alerts\Updates;

use Carbon\Carbon;
use App\FlightNumber;
use App\FlightStatus;
use App\User;
use LArtie\FlightStatsApi\Core\Methods\FlightStatus as FsApiMethodFlightStatus;
use LArtie\FlightStatsApi\Core\Objects\StatusFlight;

/**
 * Class UpdatesHandler
 * @package App\Alerts\Updates
 */
final class UpdatesHandler
{
    /**
     * @var User
     */
    private $user;

    /**
     * UpdatesHandler constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handler()
    {
        $now = Carbon::now($this->user->googleAccount->calendar->timezone);

        /** @var FlightStatus[] $flights */
        $flights = $this->user->flights()->where('departure_date_local', '>', $now)->orderBy('departure_date_local', 'asc')->get();

        foreach ($flights as $flight) {

            $fsApi = new FsApiMethodFlightStatus();

            if ($flight->flight_id === null) {
                /** @var FlightNumber $fn */
                $flightNumber = $flight->flightNumbers()->first();
                $flightStatus = $fsApi->departingOnDate($flightNumber->carrier_code, $flightNumber->flight_number, $flightNumber->departure_time->year, $flightNumber->departure_time->month, $flightNumber->departure_time->day);
            } else {
                $flightStatus = $fsApi->byFlightId($flight->flight_id);
            }

            /** @var StatusFlight $flightStatus */
            $flightStatus->getAirportResources();
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