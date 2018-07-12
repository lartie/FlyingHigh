<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LArtie\Google\Models\Account;
use LArtie\Google\Models\Channel;

/**
 * Class User
 * @package App
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property object $settings
 * @property integer $telegram_id
 * @property Account $googleAccount
 * @property Channel[] $googleChannel
 * @property UserVerification[] $verify
 * @property FlightStatus[] $flights
 */
final class User extends Model
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $connection = 'flyinghigh';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'telegram_id',
        'settings',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'settings' => 'object',
    ];

    /**
     * @return string
     */
    public function getFullNameAttribute() : string
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    /**
     * @return HasMany
     */
    public function googleChannel() : HasMany
    {
        return $this->hasMany(Channel::class);
    }

    /**
     * @return HasOne
     */
    public function googleAccount() : HasOne
    {
        return $this->hasOne(Account::class);
    }

    /**
     * Get flights
     *
     * @return BelongsToMany
     */
    public function flights() : BelongsToMany
    {
        return $this->belongsToMany(FlightStatus::class, 'user_flight', 'user_id', 'flight_id')->withTimestamps();
    }

    /**
     * Get user verification token
     *
     * @return HasMany
     */
    public function verify() : HasMany
    {
        return $this->hasMany(UserVerification::class);
    }
}
