<?php

namespace App\Repositories;

use App\FlightStatus;

/**
 * Class FlightStatusRepository
 * @package App\Repositories
 */
final class FlightStatusRepository
{
    /**
     * @param $data
     * @return FlightStatus|null
     */
    public function getByIataCodesViaLocalTime(array $data)
    {
        return FlightStatus::where($data)->first();
    }

    /**
     * @param int $id
     * @return FlightStatus|null
     */
    public function getByFlightId(int $id)
    {
        return FlightStatus::where('flight_id', $id)->first();
    }

    /**
     * @param array $data
     * @return FlightStatus
     */
    public function create(array $data) : FlightStatus
    {
        return FlightStatus::create($data);
    }

    /**
     * @param array $data
     * @return FlightStatus|null
     */
    public function exists(array $data)
    {
        return $this->getByIataCodesViaLocalTime($data);
    }

    /**
     * @param array $data
     * @return FlightStatus
     */
    public function createIfNotExists(array $data) : FlightStatus
    {
        $item = $this->exists($data);

        if ($item) {
            return $item;
        }
        return $this->create($data);
    }
}
