<?php

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
