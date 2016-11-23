<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

namespace LArtie\TelegramBot\Commands;

use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Commands\Command;

/**
 * Class SettingsCommand
 * @package LArtie\TelegramBot\Commands
 *
 */
final class SettingsCommand extends Command
{
    use BotLogsActivity;

    /**
     * @var string Command Name
     */
    protected $name = "settings";

    /**
     * @var string Command Description
     */
    protected $description = "Settings";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $keyboard = [
            ['🌐 Connect Google'],
            ['⬅️ Back'],
        ];

        if ($arguments['user']->googleAccount) {
            $keyboard = [
                ['🆘 Contact support'],
                ['🚫 Disconnect Google account'],
                ['⬅️ Back'],
            ];
        }

        $response = $arguments['responseMessage'] ?? 'What do you want to do?';

        $this->replyWithMessage([
            'text' => $response,
            'reply_markup' => \GuzzleHttp\json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ]),
        ]);

        $this->writeLog($this->getUpdate()->getMessage(), $response);
    }
}