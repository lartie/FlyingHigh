<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Alert
 * @package LArtie\FlyingHighBot\Models
 *
 * @property integer $alert_id
 * @property string $name
 * @property string $description
 * @property boolean $active
 */
final class Alert extends Model
{
    /**
     * @var string
     */
    protected $table = 'fs_alerts';

    /**
     * @var string
     */
    protected $connection = 'flyinghigh';

    /**
     * @var array
     */
    protected $fillable = [
        'alert_id',
        'name',
        'description',
        'active',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function flight() : BelongsTo
    {
        return $this->belongsTo(FlightStatus::class);
    }

    /**
     * @return BelongsTo
     */
    public function event() : BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
