<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 9.11.2016
 */

namespace App\Repositories;

use LArtie\Airports\Models\Airport;

/**
 * Class AirportRepository
 * @package LArtie\FlyingHighBot\Repositories
 */
final class AirportRepository
{
    /**
     * @param string $iata
     * @param array $select
     * @param bool $withCity
     * @return Airport|null
     */
    public static function getByIataCode(string $iata, array $select = ['*'], bool $withCity = false)
    {
        $query = Airport::select($select)->where('iata_code', $iata);

        if ($withCity) {
            $query->with('city');
        }

        return $query->first();
    }
}