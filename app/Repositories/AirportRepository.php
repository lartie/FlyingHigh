<?php

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
