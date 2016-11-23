<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

namespace LArtie\TelegramBot\Commands;

use LArtie\TelegramBot\Repositories\UserRepository;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Commands\Command;

/**
 * Class HomeCommand
 * @package LArtie\TelegramBot\Commands
 *
 */
final class HomeCommand extends Command
{
    use BotLogsActivity;

    /**
     * @var string Command Name
     */
    protected $name = "home";

    /**
     * @var string Command Description
     */
    protected $description = "To home";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $keyboard = [
            'keyboard' => [],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];

        if ($arguments['user']->googleAccount) {
            if (UserRepository::flightsCount($arguments['user'])) {
                $keyboard['keyboard'][] = ['✈️ My flights'];
            }
        } else {
            $keyboard['keyboard'][] = ['🌐 Connect Google'];
        }

        $keyboard['keyboard'][] = ['⚒ Settings'];

        $response = $arguments['responseMessage'] ?? 'Choose:';

        $this->replyWithMessage([
            'text' => $response,
            'reply_markup' => \GuzzleHttp\json_encode($keyboard),
        ]);

        $this->writeLog($this->getUpdate()->getMessage(), $response);
    }
}