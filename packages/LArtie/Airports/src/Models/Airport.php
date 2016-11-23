<?php

namespace LArtie\Airports\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Airport
 * @package App
 *
 * @property integer $id
 * @property string $iata_code
 * @property string $icao_code
 * @property integer $gmt_offset
 * @property string $timezone
 * @property City $city
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Airport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'iata_code',
        'icao_code',
        'gmt_offset',
        'timezone',
    ];

    /**
     * @var string
     */
    protected $connection = 'flyinghigh';

    /**
     * Город аэропорта
     *
     * @return BelongsTo
     */
    public function city() : BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
