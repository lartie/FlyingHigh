<?php

namespace LArtie\TelegramBot\Commands;

use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class SupportCommand
 * @package LArtie\TelegramBot\Commands
 *
 */
final class SupportCommand extends Command
{
    use BotLogsActivity;

    /**
     * @var string Command Name
     */
    protected $name = "settings/support";

    /**
     * @var string Command Description
     */
    protected $description = "Support";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $response = trans('messages.help');

        $keyboard = Keyboard::make([
            'inline_keyboard' => [
                [
                    [
                        'text' => trans('messages.support.contact'),
                        'url' => config('telegrambot.support'),
                    ],
                ],
            ],
        ]);

        $this->replyWithMessage([
            'text' => $response,
            'reply_markup' => $keyboard,
        ]);

        $arguments['responseMessage'] = '⬆️ Tap here to help ⬆️';

        (new SettingsCommand())->make($this->telegram, $arguments, $this->update);

        $this->writeLog($this->getUpdate()->getMessage(), $response);
    }
}
