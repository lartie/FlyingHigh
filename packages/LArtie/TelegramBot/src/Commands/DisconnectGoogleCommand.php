<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

namespace LArtie\TelegramBot\Commands;

use LArtie\Backend\Core\GoogleAccount;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Commands\Command;

/**
 * Class DisconnectGoogleCommand
 * @package LArtie\TelegramBot\Commands
 *
 */
final class DisconnectGoogleCommand extends Command
{
    use BotLogsActivity;
    
    /**
     * @var string Command Name
     */
    protected $name = "settings/disconnect";

    /**
     * @var string Command Description
     */
    protected $description = "disconnect google account";

    /**
     * @inheritdoc
     * @throws \Exception
     * @throws \Throwable
     */
    public function handle($arguments)
    {
        $googleAccount = new GoogleAccount($this->arguments['user']);
        $disconnect = $googleAccount->disconnect();

        $response = view('telegram.google.disconnect')->with('disconnect', $disconnect)->render();

        $keyboard = [
            'keyboard' => [
                ['🚀 Start'],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $this->replyWithMessage([
            'text' => $response,
            'parse_mode' => 'HTML',
            'reply_markup' => \GuzzleHttp\json_encode($keyboard),
        ]);

        $this->writeLog($this->getUpdate()->getMessage(), $response);
    }
}