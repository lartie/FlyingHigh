<?php

namespace App\Core;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Wrappers\GoogleApiWrapper;
use LArtie\Google\Models\Account;

/**
 * Class GoogleCalendar
 * @package LArtie\FlyingHighBot\Core
 *
 * Обрабатывает события календаря
 */
final class GoogleCalendar
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Google_Client
     */
    private $client;

    /**
     * @var Account
     */
    private $account;

    /**
     * GoogleEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        $this->client = GoogleApiWrapper::getClient();

        $this->account = $this->user->googleAccount()->with('token')->with('calendar')->first();
    }

    /**
     * Запуск сканирования, извлечения и проверки непроверенных событий календаря
     *
     * @return array
     */
    public function sync()
    {
        $data = [];

        if ($this->account && $this->account->token) {

            $access = json_encode(GoogleApiWrapper::generateAccessToken($this->account->token));
            $this->client->setAccessToken($access);

            $calendar = $this->account->calendar;

            if ($calendar) {
                $events = $this->loadEvents($this->account->email, $this->account->calendar->sync_token);

                $calendar->update([
                    'sync_token' => $events['syncToken']
                ]);
            } else {
                $events = $this->loadEvents($this->account->email);

                $calendar = $this->account->calendar()->create([
                    'calendar_id' => $this->account->email,
                    'timezone' => $events['tz'],
                    'sync_token' => $events['syncToken'],
                ]);
            }

            foreach ($events['flights'] as $key => $event) {
                try {
                    $exists = $calendar->event()->where('event_id', $event['event_id'])->first();

                    if (!$exists) {
                        $data[] = $calendar->event()->create($event);
                    }
                } catch (QueryException $e) {
                    Log::critical('Error: ' .  $e->getCode() . ' ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                 }
            }
        }
        return $data;
    }

    /**
     * @param $email
     * @param null $syncToken
     * @return array
     */
    private function loadEvents($email, $syncToken = null)
    {
        $calendarService = new Google_Service_Calendar($this->client);
        $nextPageToken = null;
        $options = [];
        $flights = [];

        if ($syncToken) {
            $options['syncToken'] = $syncToken;
        }

        do {
            if ($nextPageToken) {
                $options['pageToken'] = $nextPageToken;
            }

            try {
                $events = $calendarService->events->listEvents($email, $options);
            } catch (Google_Service_Exception $exception) {

                $options['syncToken'] = null;
                $events = $calendarService->events->listEvents($email, $options);

                Log::error($exception->getMessage());
            }

            $flightEvents = $this->getFlightEvents($events->getItems(), $events->getTimeZone());

            $flights = array_merge($flights, $flightEvents);

        } while ($nextPageToken = $events->getNextPageToken());

        return [
            'flights' => $flights,
            'syncToken' => $events->getNextSyncToken(),
            'tz' => $events->getTimeZone(),
        ];
    }

    /**
     * Перебирает события и извлекает те, которые подходят по шаблону в названии
     *
     * @param Google_Service_Calendar_Event[] $events
     * @param $timezone
     * @return array
     */
    private function getFlightEvents($events, $timezone)
    {
        $flights = [];

        foreach ($events as $item) {
            if (str_contains($item->getSummary(), ['Авиаперелет', 'Flight'])) {

                $start = new Carbon($item->getStart()->getDateTime());
                $end = new Carbon($item->getEnd()->getDateTime());

                $currentDate = Carbon::now()->setTimezone($timezone);

                if ($currentDate->timestamp < $start->timestamp) {

                    $to = trim(str_replace(['Авиаперелет в г.', 'Flight to'], '', $item->getSummary()));

                    $flights[] = [
                        'event_id' => $item->getId(),
                        'from' => $item->getLocation(),
                        'to' => $to,
                        'start_timezone' => $start->timezoneName,
                        'start' => $start->setTimezone('UTC'),
                        'end_timezone' => $end->timezoneName,
                        'end' => $end->setTimezone('UTC'),
                    ];
                }
            }
        }
        return $flights;
    }
}
