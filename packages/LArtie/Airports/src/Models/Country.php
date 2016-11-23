<?php

namespace LArtie\Airports\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 * @package App
 *
 * @property integer $id
 * @property string $name_en
 * @property string $name_ru
 * @property string $iso_code
 * @property City[] $cities
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Country extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_en',
        'name_ru',
        'iso_code',
    ];

    /**
     * @var string
     */
    protected $connection = 'flyinghigh';

    /**
     * Города в стране
     *
     * @return HasMany
     */
    public function cities() : HasMany
    {
        return $this->hasMany(City::class);
    }
}
