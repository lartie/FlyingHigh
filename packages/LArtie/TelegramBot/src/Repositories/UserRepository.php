<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 22.11.2016
 */

namespace LArtie\TelegramBot\Repositories;

use Carbon\Carbon;
use App\User;

/**
 * Class UserRepository
 * @package LArtie\TelegramBot\Repositories
 */
final class UserRepository
{
    /**
     * @param User $user
     * @return bool
     */
    public static function flightsCount(User $user) : bool
    {
        if (!isset($user->googleAccount->calendar)) {
            return false;
        }
        $now = Carbon::now($user->googleAccount->calendar->timezone);
        $invalidFlights = $user->googleAccount->calendar->event()->where('start',  '>', $now)->count();

        return $invalidFlights > 0;
    }
}