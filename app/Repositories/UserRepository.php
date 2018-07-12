<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\FlightStatus;
use App\User;

/**
 * Class UserRepository
 * @package App\Repositories
 */
final class UserRepository
{
    /**
     * @param array $data
     * @return User
     */
    public static function create(array $data) : User
    {
        if (!isset($data['settings'])) {
            $data['settings'] = [
                'lang' => 'en',
            ];
        }
        return User::create($data);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public static function getByTelegramID(int $id)
    {
        return User::where('telegram_id', $id)->first();
    }

    /**
     * @param array $data
     * @return User
     */
    public static function firstOrCreate(array $data) : User
    {
        $user = UserRepository::getByTelegramID($data['telegram_id']);

        if ($user) {
            return $user;
        }
        return UserRepository::create($data);
    }

    /**
     * @param User $user
     * @param FlightStatus $flight
     * @return bool
     */
    public static function attachFlight(User $user, FlightStatus $flight) : bool
    {
        if (UserRepository::flightExists($user->id, $flight->id)) {
            return false;
        }
        $user->flights()->attach($flight);

        return true;
    }

    /**
     * @param int $userId
     * @param int $flightId
     * @return mixed
     */
    public static function flightExists(int $userId, int $flightId)
    {
        return DB::connection('flyinghigh')->table('user_flight')->where('user_id', $userId)->where('flight_id', $flightId)->first();
    }
}
