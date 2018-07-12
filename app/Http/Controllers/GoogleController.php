<?php

namespace App\Http\Controllers;

use App\Jobs\GoogleCalendarSync;
use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Oauth2_Userinfoplus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Core\GoogleAccount;
use App\Repositories\UserRepository;
use App\Repositories\UserVerificationRepository;
use App\Wrappers\GoogleApiWrapper;
use LArtie\Google\Models\Channel;
use LArtie\TelegramBot\Traits\BotLogsActivity;
use Telegram\Bot\Actions;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Exceptions\TelegramSDKException;

/**
 * Class GoogleController
 * @package App\Http\Controllers
 */
final class GoogleController extends Controller
{
    use BotLogsActivity;

    /**
     * Разрешения для приложения
     *
     * @var array
     */
    private $scopes = [
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
    ];

    /**
     * Разрешение на автономное использование аккаунта
     *
     * @var string
     */
    private $accessType = 'offline';

    /**
     * @var string
     */
    private $approvalPrompt = 'force';

    /**
     *
     * @var Google_Client
     */
    private $client;

    /**
     * GoogleController constructor.
     */
    public function __construct()
    {
        $client = GoogleApiWrapper::getClient();

        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope($this->scopes);
        $client->setAccessType($this->accessType);
        $client->setApprovalPrompt($this->approvalPrompt);

        $this->client = $client;
    }

    /**
     * Запрос разрешения на авторизацию google аккаунта для данного пользователя.
     * Выполняет проверку по cookie и по переданному параметру token
     *
     * @param Request $request
     * @param int $id
     * @param string $token
     * @return RedirectResponse|View
     */
    public function auth(Request $request, int $id, string $token)
    {
        $user = UserRepository::getByTelegramID($id);

        if ($user) {
            $userVerificationRepository = new UserVerificationRepository($user);
            $isValidToken = $userVerificationRepository->getIfActive($token);

            if ($isValidToken) {
                if (!$user->googleAccount) {

                    if ($request->cookie('tg_user_id') == $id) {
                        return redirect($this->client->createAuthUrl());
                    }
                    return redirect($request->url())->withCookies([
                        cookie('tg_user_id', $id, 600),
                        cookie('tg_token', $token, 600),
                    ]);
                } else {
                    $messageId = 'errors.google.account.notExists';
                }
            } else {
                $messageId = 'errors.token.invalid';
            }
        } else {
            $messageId = 'errors.user.unknown';
        }
        return view('errors.error')->with('message', trans($messageId));
    }

    /**
     * Коллбек для авторизации пользователя
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|View
     * @throws TelegramSDKException
     */
    public function callback(Request $request)
    {
        if ($request->input('code') && $request->cookie('tg_user_id') && $request->cookie('tg_token')) {

            $user = UserRepository::getByTelegramID($request->cookie('tg_user_id'));

            if ($user) {
                $auth = json_decode($this->client->authenticate($request->input('code')));

                $oauth2 = new Google_Service_Oauth2($this->client);

                $googleData = $oauth2->userinfo->get();

                $googleAccount = new GoogleAccount($user);

                $userVerificationRepository = new UserVerificationRepository($user);
                $userVerificationRepository->deactivate($request->cookie('tg_token'));

                $result = $googleAccount->connect($this->prepareUserData($googleData, $auth));

                $telegram = new Telegram(config('telegrambot.token'));

                $telegram->sendMessage([
                    'chat_id' => $user->telegram_id,
                    'text' => $result['message'] . PHP_EOL . PHP_EOL . trans('messages.flight.searching'),
                ]);

                $telegram->sendChatAction([
                    'chat_id' => $user->telegram_id,
                    'action' => Actions::TYPING,
                ]);

                return redirect()->to(config('telegrambot.url'));
            }
            return view('errors.error')->with('message', trans('errors.user.unknown'));
        }
        return view('errors.error')->with('message', trans('errors.unknown'));
    }

    /**
     * Метод обрабатывающий измененеия в календаре пользователя
     * Подробнее: https://developers.google.com/google-apps/calendar/v3/push
     *
     * @param Request $request
     */
    public function webhook(Request $request)
    {
        $googleChannelId = $request->header('x-goog-channel-id');

        if ($googleChannelId) {
            /** @var Channel $channel */
            $channel = Channel::where('channel_id', $googleChannelId)->where('lock', false)->with('user')->first();

            if ($channel) {
                Log::info('Run: ' . $channel->user->telegram_id);

                $job = (new GoogleCalendarSync($channel->user, GoogleCalendarSync::RESPONSE_IF_NOT_EMPTY))->onQueue('google-events');
                dispatch($job);
            }
        }
    }

    /**
     * Подтверждение причастности к домену
     *
     * @return View
     */
    public function confirm() : View
    {
        return view('google.confirm');
    }

    /**
     * Предварительная обработка данных
     *
     * @param Google_Service_Oauth2_Userinfoplus $user
     * @param object $auth
     * @return array
     */
    private function prepareUserData(Google_Service_Oauth2_Userinfoplus $user, $auth) : array
    {
        return [
            'profile' => [
                'uid' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ],
            'auth' => [
                'access_token' => $auth->access_token,
                'refresh_token' => $auth->refresh_token,
                'token_type' => $auth->token_type,
                'expires_in' => $auth->expires_in,
                'id_token' => $auth->id_token,
                'created' => $auth->created,
            ]
        ];
    }
}
