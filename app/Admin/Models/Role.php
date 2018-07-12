<?php

namespace App\Admin\Models;

use Zizaco\Entrust\EntrustRole;

/**
 * Class Role
 * @package App
 *
 * @property string $name
 * @property string $display_name
 */
class Role extends EntrustRole
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name'
    ];
}
