<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\UserRepository;

/**
 * Class TelegramLog
 * @package App
 *
 * @property integer $id
 * @property string $from_id
 * @property string $to_id
 * @property string $from
 * @property string $to
 * @property string $message
 * @property Carbon $sent_at
 */
class TelegramLog extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $connection = 'flyinghigh';

    /**
     * @var array
     */
    protected $fillable = [
        'from_id',
        'to_id',
        'message',
        'sent_at',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'sent_at',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'from',
        'to',
    ];

    /**
     * @return string
     */
    public function getFromAttribute()
    {
        if ($this->attributes['from_id'] === 'FlyingHighBot' || $this->attributes['from_id'] === 'TelegramBot') {
            return $this->attributes['from_id'];
        }
        return UserRepository::getByTelegramID($this->attributes['from_id'])->username;
    }

    /**
     * @return string
     */
    public function getToAttribute()
    {
        if ($this->attributes['to_id'] === 'FlyingHighBot' || $this->attributes['to_id'] === 'TelegramBot') {
            return $this->attributes['to_id'];
        }

        return UserRepository::getByTelegramID($this->attributes['to_id'])->username;
    }
}
