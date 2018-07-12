<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class FlightNumber
 * @package App
 *
 * @property string $carrier_code
 * @property integer $flight_number
 * @property FlightStatus $flightStatus
 * @property Carbon $departure_time
 * @property Carbon $arrival_time
 */
final class FlightNumber extends Model
{
    /**
     * @var string
     */
    protected $table = 'fs_flight_numbers';

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
        'carrier_code',
        'flight_number',
        'departure_time',
        'arrival_time'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'departure_time',
        'arrival_time',
    ];

    /**
     * @param $value
     */
    public function setDepartureTimeAttribute($value)
    {
        if ($value instanceof Carbon) {
            $date = $value;
        } else {
            $date = Carbon::parse($value);
        }

        $this->attributes['departure_time'] = $date;
    }

    /**
     * @param $value
     */
    public function setArrivalTimeAttribute($value)
    {
        if ($value instanceof Carbon) {
            $date = $value;
        } else {
            $date = Carbon::parse($value);
        }

        $this->attributes['arrival_time'] = $date;
    }

    /**
     * @return BelongsTo
     */
    public function flightStatus() : BelongsTo
    {
        return $this->belongsTo(FlightStatus::class, 'flight_id');
    }
}
