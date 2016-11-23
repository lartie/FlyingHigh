<?php

namespace LArtie\Airports\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class City
 * @package App
 *
 * @property integer $id
 * @property string $name_en
 * @property string $name_ru
 * @property Airport[] $airports
 * @property Country $country
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_en',
        'name_ru',
    ];

    /**
     * @var string
     */
    protected $connection = 'flyinghigh';

    /**
     * Аэропорты относящиеся к городу
     * @return HasMany
     */
    public function airports() : HasMany
    {
        return $this->hasMany(Airport::class);
    }

    /**
     * Страна города
     * @return BelongsTo
     */
    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
