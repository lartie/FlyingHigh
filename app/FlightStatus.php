<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class FlightStatus
 * @package App
 *
 * @property integer $id
 * @property integer $flight_id
 * @property string $departure_iata
 * @property string $arrival_iata
 * @property string $status
 * @property string $departure_gate
 * @property string $departure_terminal
 * @property Carbon $departure_date_local
 * @property Carbon $departure_date_utc
 * @property string $arrival_gate
 * @property string $arrival_terminal
 * @property Carbon $arrival_date_local
 * @property Carbon $arrival_date_utc
 */
final class FlightStatus extends Model
{
    /**
     * @var string
     */
    protected $table = 'fs_flight_status';

    /**
     * @var string
     */
    protected $connection = 'flyinghigh';

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * @var array
     */
    protected $fillable = [
        'flight_id',
        'departure_iata',
        'arrival_iata',
        'status',
        'departure_gate',
        'departure_terminal',
        'departure_date_local',
        'departure_date_utc',
        'arrival_gate',
        'arrival_terminal',
        'arrival_date_local',
        'arrival_date_utc',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'departure_date_local',
        'departure_date_utc',
        'arrival_date_local',
        'arrival_date_utc',
    ];

    /**
     * @param $value
     */
    public function setDepartureDateLocalAttribute($value)
    {
        if ($value instanceof Carbon) {
            $date = $value;
        } else {
            $date = Carbon::parse($value);
        }

        $this->attributes['departure_date_local'] = $date;
    }

    /**
     * @param $value
     */
    public function setDepartureDateUtcAttribute($value)
    {
        if ($value instanceof Carbon) {
            $date = $value;
        } else {
            $date = Carbon::parse($value);
        }

        $this->attributes['departure_date_utc'] = $date;
    }

    /**
     * @param $value
     */
    public function setArrivalDateLocalAttribute($value)
    {
        if ($value instanceof Carbon) {
            $date = $value;
        } else {
            $date = Carbon::parse($value);
        }

        $this->attributes['arrival_date_local'] = $date;
    }

    /**
     * @param $value
     */
    public function setArrivalDateUtcAttribute($value)
    {
        if ($value instanceof Carbon) {
            $date = $value;
        } else {
            $date = Carbon::parse($value);
        }

        $this->attributes['arrival_date_utc'] = $date;
    }

    /**
     * Get alerts
     *
     * @return HasMany
     */
    public function alerts() : HasMany
    {
        return $this->hasMany(Alert::class, 'flight_id');
    }

    /**
     * Get flight numbers
     *
     * @return HasMany
     */
    public function flightNumbers() : HasMany
    {
        return $this->hasMany(FlightNumber::class, 'flight_id');
    }

    /**
     * Get users
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_flight', 'flight_id', 'user_id')->withTimestamps();
    }
}
