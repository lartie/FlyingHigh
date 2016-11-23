<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 2.9.2016
 */

namespace App\Repositories;

use App\FlightNumber;
use App\FlightStatus;

/**
 * Class FlightNumberRepository
 * @package App\Repositories
 */
final class FlightNumberRepository
{
    /**
     * @var FlightStatus
     */
    private $flightStatus;

    /**
     * FlightNumberRepository constructor.
     *
     * @param FlightStatus $flightStatus
     */
    public function __construct(FlightStatus $flightStatus)
    {
        $this->flightStatus = $flightStatus;
    }

    /**
     * @param array $data
     * @return FlightNumber
     */
    public function create(array $data) : FlightNumber
    {
        return $this->flightStatus->flightNumbers()->create($data);
    }

    /**
     * @param array $data
     * @return FlightNumber|null
     */
    public function exists(array $data)
    {
        return $this->flightStatus->flightNumbers()->where($data)->first();
    }

    /**
     * @param array $data
     * @return FlightNumber
     */
    public function createIfNotExists(array $data) : FlightNumber
    {
        $item = $this->exists($data);

        if ($item) {
            return $item;
        }

        return $this->create($data);
    }
}