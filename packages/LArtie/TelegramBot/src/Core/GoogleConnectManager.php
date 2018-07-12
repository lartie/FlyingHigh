<?php

namespace LArtie\TelegramBot\Core;

use App\User;
use App\Repositories\UserVerificationRepository;

/**
 * Class GoogleConnectManager
 * @package LArtie\TelegramBot\Models
 *
 * Менеджер подключения google аккаунта.
 *
 */
final class GoogleConnectManager
{
    /**
     * @var User
     */
    private $user;

    /**
     * GoogleConnectManager constructor.
     *
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Создает telegram ответ, содержащий в себе текст + ссылку на подключение аккаунта
     *
     * @return array
     */
    public function make()
    {
        $userVerificationRepository = new UserVerificationRepository($this->user);
        $token = $userVerificationRepository->create();

        return [
            'message' => trans('messages.google.connect'),
            'keyboard' => $this->getKeyboard($token->token),
        ];
    }


    /**
     * Возвращает клавиатуру
     *
     * @param $token
     * @return array
     */
    private function getKeyboard($token)
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Connect Google',
                        'url' => $this->getUri($this->user->telegram_id, $token)
                    ],
                ],
            ],
        ];
    }

    /**
     * Генерирует ссылку для подлкючения пользователя
     *
     * @param $user
     * @param $token
     * @return string
     */
    private function getUri($user, $token)
    {
        return route('telegram.flyinghighbot.google.auth', [
            'user' => $user,
            'token' => $token,
        ]);
    }
}
