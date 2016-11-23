<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 2.9.2016
 */

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class EventRepository
 * @package App\Repositories
 */
final class EventRepository
{
    /**
     * @param $id
     * @return mixed
     */
    public static function getPrepareEventByEventID($id)
    {
        $now = Carbon::now()->toAtomString();

        return DB::connection('flyinghigh')->select(self::getPrepareEventQueryById(), [
            $now,
            $id,
            $now,
            $id,
        ]);
    }

    /**
     * @return string
     */
    private static function getPrepareEventQueryById()
    {
        return "
          SELECT `cities`.`name_en`, `airports`.`iata_code`, `airports`.`timezone`, `google_events`.`start`, `google_events`.`start_timezone`, `google_events`.`end_timezone`, `google_events`.`end`, `google_events`.`to`, `google_events`.`event_id` 
          FROM `google_events` 
          INNER JOIN `cities` 
          ON INSTR(BINARY google_events.from, cities.name_ru)   
          AND `cities`.`name_ru` != '' 
          INNER JOIN `airports` 
          ON INSTR(BINARY google_events.from, airports.iata_code)   
          AND `cities`.`id` = `airports`.`city_id` 
          WHERE `google_events`.`start` > ? 
          AND `google_events`.`id` = ?
          AND `google_events`.`identified` = FALSE

          UNION
          
          SELECT `cities`.`name_en`, `airports`.`iata_code`, `airports`.`timezone`, `google_events`.`start`, `google_events`.`start_timezone`, `google_events`.`end_timezone`, `google_events`.`end`, `google_events`.`to`, `google_events`.`event_id`
          FROM `google_events` 
          INNER JOIN `cities` 
          ON INSTR(BINARY google_events.from, cities.name_en)
          INNER JOIN `airports` 
          ON INSTR(BINARY google_events.from, airports.iata_code)
          AND `cities`.`id` = `airports`.`city_id` 
          WHERE `google_events`.`start` > ? 
          AND `google_events`.`id` = ?
          AND `google_events`.`identified` = FALSE
";
    }
}