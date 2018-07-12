<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserVerification
 * @package App
 *
 * @property string $token
 * @property boolean $active
 */
final class UserVerification extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_verification';

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
        'token',
        'active',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * User
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
