<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

use SleepingOwl\Admin\Navigation\Page;

return [
    [
        'title' => 'Dashboard',
        'icon'  => 'fa fa-dashboard',
        'url'   => route('admin.dashboard'),
    ],

    [
        'title' => 'Flying High',
        'pages' => [
            (new Page(\App\User::class))
                ->setIcon('fa fa-paper-plane'),
            (new Page(\LArtie\Google\Models\Calendar::class))
                ->setIcon('fa fa-calendar'),
            (new Page(\LArtie\Google\Models\Event::class))
                ->setIcon('fa fa-calendar-o'),
            (new Page(\LArtie\Google\Models\Account::class))
                ->setIcon('fa fa-google-plus'),
            (new Page(\LArtie\Google\Models\Channel::class))
                ->setIcon('fa fa-rss'),
            (new Page(\App\TelegramLog::class))
                ->setIcon('fa fa-file-text-o'),

        ],
        'icon'  => 'fa fa-star',
    ],

    [
        'title' => 'Flights',
        'pages' => [
            (new Page(\App\FlightNumber::class)),
            (new Page(\App\FlightStatus::class)),
            (new Page(\App\Alert::class)),
            (new Page(\App\Event::class)),
        ],
        'icon'  => 'fa fa-fighter-jet',
    ],

    [
        'title' => 'Airports',
        'pages' => [
            (new Page(\LArtie\Airports\Models\Country::class))
                ->setIcon('fa fa-globe'),
            (new Page(\LArtie\Airports\Models\City::class))
                ->setIcon('fa fa-map-marker'),
            (new Page(\LArtie\Airports\Models\Airport::class))
                ->setIcon('fa fa-plane'),
        ],
        'icon'  => 'fa fa-fighter-jet',
    ],

    [
        'title' => 'Permissions',
        'pages' => [
            (new Page(\App\Admin\Models\User::class))
                ->setIcon('fa fa-users'),
            (new Page(\App\Admin\Models\Role::class))
                ->setIcon('fa fa-users'),
            (new Page(\App\Admin\Models\Permission::class))
                ->setIcon('fa fa-users'),
        ],
        'icon'  => 'fa fa-unlock-alt',
    ],

    [
        'title' => 'Information',
        'icon'  => 'fa fa-exclamation-circle',
        'url'   => route('admin.information'),
    ],

    [
        'title' => 'Logout',
        'icon'  => 'fa fa-sign-out',
        'url'   => route('auth.logout.get'),
    ],
];