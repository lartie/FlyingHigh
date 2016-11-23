<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 18.11.2016
 */

namespace LArtie\TelegramBot\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Repositories\UserRepository;
use LArtie\LaravelBotan\Facades\Botan;
use LArtie\TelegramBot\Callbacks\CallbackCommand;
use LArtie\TelegramBot\Callbacks\ShowFlightsCallback;
use LArtie\TelegramBot\Commands\HomeCommand;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use LogicException;
use Telegram\Bot\Actions;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

/**
 * Class MainController
 * @package LArtie\TelegramBot\Controllers
 *
 * Класс контроллер обрабатывающий приходящие от telegram запросы
 */
final class MainController extends Controller
{
    use BotLogsActivity;

    /**
     * Вебхук для приема комманд с бота
     *
     * @param Request $request
     * @throws TelegramSDKException
     * @throws LogicException
     * @throws Exception
     */
    public function webhook(Request $request)
    {
        $update = new Update(json_decode($request->getContent()));
        $telegram = new Telegram(config('telegrambot.token'));

        $chat = $update->getChat();

        $user = UserRepository::firstOrCreate([
            'telegram_id' => $chat->getId(),
            'first_name' => $chat->getFirstName(),
            'last_name' => $chat->getLastName(),
            'username' => $chat->getUsername(),
        ]);

        $arguments = [
            'user' => $user,
        ];

        if ($update->getMessage() instanceof Message) {

            $telegram->sendChatAction([
                'chat_id' => $chat->getId(),
                'action' => Actions::TYPING,
            ]);

            $this->commandsHandler($telegram, $update, $arguments);

            $this->writeLog($update->getMessage());

        } else if ($update->getCallbackQuery() instanceof CallbackQuery) {
            $this->callbackHandler($telegram, $update, $arguments);

        } else {
            $arguments['responseMessage'] = trans('messages.unknownCommand');

            (new HomeCommand())->make($telegram, $arguments, $update);
        }
    }

    /**
     * @param Telegram $telegram
     * @param Update $update
     * @param array $arguments
     * @throws \Exception
     */
    private function commandsHandler(Telegram $telegram, Update $update, array $arguments)
    {
        $message = $update->getMessage();

        $text = $this->prepareCommand($message->getText());


        if (starts_with($text, 'start')) {
            $params = explode(' ', $text);

            $text = $params[0];
            unset($params[0]);
            $arguments['args'] = $params;
        }

        /** @var array $map */
        $map = config('telegrambot.map');

        if (!empty($map)) {
            /** @var Command $command */
            $command = null;

            foreach ($map as $item) {
                if (in_array($text, $item['aliases'], true)) {
                    $command = new $item['controller'];
                }
            }
            if ($command === null) {
                $command = new $map[0]['controller'];
                $arguments['responseMessage'] = trans('messages.unknownCommand');
            }

            Botan::track($message->toArray(), $command->getName());

            $command->make($telegram, $arguments, $update);
        }
    }

    /**
     * Обработчик для callbackQuery из telegram бота.
     *
     * @param Telegram $telegram
     * @param Update $update
     * @param array $arguments
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    private function callbackHandler(Telegram $telegram, Update $update, array $arguments)
    {
        $callbackQuery = $update->getCallbackQuery();

        $callbacks = [
            ShowFlightsCallback::class,
        ];

        foreach ($callbacks as $callback) {
            /** @var CallbackCommand $callbackCommand */
            $callbackCommand = new $callback;

            $command = $this->parseCallbackCommand($callbackQuery->getData());

            $arguments = array_merge($command['args'], $arguments);

            if ($callbackCommand->getName() === $command['command']) {
                $callbackCommand->make($telegram, $update, $callbackQuery, $arguments);
            }
        }
    }

    /**
     * Извлекает параметры из callback_query_data
     *
     * @param $data
     * @return mixed
     */
    private function parseCallbackCommand($data)
    {
        $url = parse_url($data);

        $command = $url['path'];
        parse_str($url['query'], $args);

        return compact('command', 'args');
    }

    /**
     * @param $string
     * @return string
     */
    private function prepareCommand($string) : string
    {
        return strtolower(trim($this->removeEmoji($string), '/ '));
    }

    /**
     * @param $text
     * @return string
     */
    public function removeEmoji($text) : string
    {
        return preg_replace('/[^a-zA-Z0-9 ]/ui', '', $text);
    }
}