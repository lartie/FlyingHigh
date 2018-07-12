<?php

namespace App\Core;

use App\Jobs\GoogleCalendarSync;
use Carbon\Carbon;
use Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Channel;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Wrappers\GoogleApiWrapper;
use LArtie\Google\Models\Account;
use LArtie\Google\Models\Channel;

/**
 * Class GoogleAccount
 * @package App\Core
 *
 * Выполняет необходимые действия по подлкючению/отключению google аккаунт
 */
final class GoogleAccount
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Account
     */
    private $account;

    /**
     * GoogleAccount constructor.
     *
     * @param User $user
     * @throws Exception
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->account = $this->user->googleAccount;
    }

    /**
     * Ассоциирует google аккаунт с пользователем и добавляет подписку на изменение календаря
     *
     * @param array $user
     * @return array
     */
    public function connect(array $user) : array
    {
        if ($this->account) {
            return [
                'message' => trans('errors.google.account.exists')
            ];
        }

        /** @var Account $account */
        $this->account = $this->user->googleAccount()->create($user['profile']);
        $this->account->token()->create($user['auth']);

        $channel = $this->addChannel();

        $job = (new GoogleCalendarSync($this->user))->onQueue('google-events');
        dispatch($job);

        if ($channel === false) {
            return [
                'message' => trans('errors.google.channel.exists')
            ];
        }
        return [
            'message' => trans('messages.google.successConnect'),
        ];
    }

    /**
     * Отключает google аккаунт для выбранного пользователя
     *
     * @return bool
     */
    public function disconnect() : bool
    {
        if (!$this->account) {
            return false;
        }

        $this->deleteChannel();
        $this->account->delete();

        $this->user->flights()->detach();

        return true;
    }

    /**
     * Подключить прослушивание событий календаря для выбранного юзера
     *
     * @return bool|Model
     */
    public function addChannel()
    {
        if ($this->user->googleChannel()->first()) {
            return false;
        }

        $client = GoogleApiWrapper::getClient();

        $access = json_encode(GoogleApiWrapper::generateAccessToken($this->account->token));
        $client->setAccessToken($access);

        $serviceCalendar = new Google_Service_Calendar($client);
        $serviceCalendarChannel = new Google_Service_Calendar_Channel();

        $serviceCalendarChannel->setId($this->getUniqueId());
        $serviceCalendarChannel->setAddress(GoogleApiWrapper::getGoogleWebhookUri());
        $serviceCalendarChannel->setType('web_hook');
        $serviceCalendarChannel->setParams([
            'ttl' => $this->getExpiresOn(),
        ]);

        $response = $serviceCalendar->events->watch($this->account->email, $serviceCalendarChannel);

        $carbon = Carbon::createFromTimestamp($response->getExpiration() / 1000);

        return $this->user->googleChannel()->create([
            'channel_id' => $response->getId(),
            'expiration' => $carbon->toDateTimeString(),
            'resource_id' => $response->getResourceId(),
        ]);
    }

    /**
     * Отключить прослушивание событий календаря для выбранного юзера
     *
     * @return bool
     * @throws Exception
     */
    public function deleteChannel() : bool
    {
        /** @var Channel $channel */
        $channel = $this->user->googleChannel()->first();

        if (!$channel) {
            return false;
        }

        $client = GoogleApiWrapper::getClient();

        $access = json_encode(GoogleApiWrapper::generateAccessToken($this->account->token));
        $client->setAccessToken($access);

        $service = new Google_Service_Calendar($client);
        $calendarChannel = new Google_Service_Calendar_Channel();

        $calendarChannel->setId($channel->channel_id);
        $calendarChannel->setResourceId($channel->resource_id);

        $service->channels->stop($calendarChannel);

        return $channel->delete();
    }

    /**
     * @return int
     */
    private function getExpiresOn() : int
    {
        return time()+3600000;
    }

    /**
     * @return string
     */
    private function getUniqueId() : string
    {
        return uniqid($this->user->telegram_id . '-');
    }
}
