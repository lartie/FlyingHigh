<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

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