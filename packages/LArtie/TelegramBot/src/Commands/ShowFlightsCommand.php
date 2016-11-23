<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

namespace LArtie\TelegramBot\Commands;

use LArtie\TelegramBot\Core\FlightsManager;
use LArtie\TelegramBot\Repositories\UserRepository;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Commands\Command;

/**
 * Class ShowFlightsCommand
 * @package LArtie\TelegramBot\Commands
 *
 */
final class ShowFlightsCommand extends Command
{
    use BotLogsActivity;
    
    /**
     * @var string Command Name
     */
    protected $name = "flights/list";

    /**
     * @var string Command Description
     */
    protected $description = "Show flights";

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function handle($arguments)
    {
        if (isset($arguments['user']->googleAccount->calendar)) {

            $keyboard = [
                'keyboard' => [],
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ];

            if (UserRepository::flightsCount($arguments['user'])) {

                $flightsManager = new FlightsManager($this->arguments['user'], 1);
                $paginate = $flightsManager->paginate();

                $response = view('telegram.showFlights')->with('flights', $paginate['items'])->render();

                $this->replyWithMessage(array_merge([
                    'text' => $response,
                ], $paginate['keyboard']));

                $this->writeLog($this->getUpdate()->getMessage(), $response);

                $keyboard['keyboard'][] = ['✈️ My flights'];
            }
            $keyboard['keyboard'][] = ['⚒ Settings'];

            $this->replyWithMessage([
                'text' => 'Choose:',
                'reply_markup' => \GuzzleHttp\json_encode($keyboard),
            ]);
        } else {
            (new StartCommand())->make($this->telegram, $arguments, $this->update);
        }
    }
}