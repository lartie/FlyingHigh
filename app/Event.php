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
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Event
 * @package App
 *
 * @property string $type
 * @property string $value
 * @property string $data_source
 * @property Carbon $datetime_recorded
 */
final class Event extends Model
{
    /**
     * @var string
     */
    protected $table = 'fs_events';

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
        'type',
        'value',
        'data_source',
        'datetime_recorded',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'datetime_recorded',
    ];

    /**
     * @param $value
     */
    public function setDatetimeRecordedAttribute($value)
    {
        if ($value instanceof Carbon) {
            $date = $value;
        } else {
            $date = Carbon::parse($value);
        }

        $this->attributes['datetime_recorded'] = $date;
    }

    /**
     * @return BelongsTo
     */
    public function alert() : BelongsTo
    {
        return $this->hasMany(Alert::class);
    }
}
