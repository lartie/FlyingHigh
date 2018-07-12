<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FlyingHighBot
    |--------------------------------------------------------------------------
    |
    | Это конфигурационный файл содержащий в себе необходимые настройки для бота.
    |
    */

    /**
     * Секретный ключ бота telegram
     */
    'token' => '212600251:AAHDdshg_avYSG_EZWdaQ2pPhgW-8l6UIX8',

    /**
     * Ссылка на бота тех поддержки
     */
    'support' => 'https://telegram.me/alexn',

    /**
     * Ссылка на бота
     */
    'url' => 'https://telegram.me/FlyingHighBot',

    'map' => [
        [
            'controller' => \LArtie\TelegramBot\Commands\HomeCommand::class,
            'aliases' => [
                'back',
                'home',
                'tohome',
            ],
        ],
        [
            'controller' => \LArtie\TelegramBot\Commands\ShowFlightsCommand::class,
            'aliases' => [
                'flights',
                'my flights',
                'get flights',
                'get my flights',
            ],
        ],
        [
            'controller' => \LArtie\TelegramBot\Commands\StartCommand::class,
            'aliases' => [
                'start',
                'index',
                'welcome',
                'connect',
                'connect google',
                'connect google account',
            ],
        ],
        [
            'controller' => \LArtie\TelegramBot\Commands\SettingsCommand::class,
            'aliases' => [
                'settings',
            ],
        ],
        [
            'controller' => \LArtie\TelegramBot\Commands\DisconnectGoogleCommand::class,
            'aliases' => [
                'disconnect',
                'disconnect google',
                'disconnect google account',
            ],
        ],
        [
            'controller' => \LArtie\TelegramBot\Commands\SupportCommand::class,
            'aliases' => [
                'help',
                'support',
                'contact support',
            ],
        ],
    ],
];
