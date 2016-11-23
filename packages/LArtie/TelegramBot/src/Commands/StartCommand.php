<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

namespace LArtie\TelegramBot\Commands;

use LArtie\TelegramBot\Core\GoogleConnectManager;
use LArtie\TelegramBot\Repositories\UserRepository;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Commands\Command;

/**
 * Class StartCommand
 * @package LArtie\TelegramBot\Commands
 *
 * Команда /start. Выполняется в момент первого обращения к боту. Регистрирует пользователя и генерирует
 * ссылку для подлкючения google аккаунта
 */
final class StartCommand extends Command
{
    use BotLogsActivity;
    
    /**
     * @var string Command Name
     */
    protected $name = "index";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        if ($arguments['user']->googleAccount) {
            $response = trans('errors.google.account.exists');

            $keyboard = [
                'keyboard' => [],
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ];

            if (UserRepository::flightsCount($arguments['user'])) {
                $keyboard['keyboard'][] = ['✈️ My flights'];
            }

            $keyboard['keyboard'][] = ['⚒ Settings'];

            $this->replyWithMessage([
                'text' => $response,
                'parse_mode' => 'HTML',
                'reply_markup' => \GuzzleHttp\json_encode($keyboard),
            ]);

            $this->writeLog($this->getUpdate()->getMessage(), $response);
        } else {
            $welcomeView = view('telegram.welcome')->render();

            $connectManager = new GoogleConnectManager($arguments['user']);
            $responseData = $connectManager->make();

            $response = $welcomeView . PHP_EOL . PHP_EOL . $responseData['message'];
            $keyboard = $responseData['keyboard'];

            $this->replyWithMessage([
                'text' => $response,
                'parse_mode' => 'HTML',
                'reply_markup' => \GuzzleHttp\json_encode($keyboard),
            ]);

            $this->writeLog($this->getUpdate()->getMessage(), $response);
        }
    }
}