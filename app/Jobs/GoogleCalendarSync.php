<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Core\GoogleCalendar;
use App\Core\FlightResponseManager;
use App\User;

/**
 * Class GoogleCalendarSync
 * @package App\Jobs
 *
 * Воркер для обнаружения подходящих под заданный шаблон событий google календаря
 */
final class GoogleCalendarSync extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var boolean
     */
    private $flag;

    /**
     * @var integer
     */
    const ALWAYS_RESPONSE = 1;

    /**
     * @var integer
     */
    const RESPONSE_IF_NOT_EMPTY = 0;

    /**
     * @var integer
     */
    const RESPONSE_IF_IDENTIFIED = 2;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param int $flag Нужен для того, чтобы определить отправлять пользователю информацию об отсутствии рейсов и о возможности повторной проверки
     */
    public function __construct(User $user, $flag = GoogleCalendarSync::ALWAYS_RESPONSE)
    {
        $this->user = $user;
        $this->flag = $flag;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $googleCalendar = new GoogleCalendar($this->user);
        $events = $googleCalendar->sync();

        if (empty($events)) {
            $googleCalendarManager = new FlightResponseManager($this->user, [], $this->flag);
            $googleCalendarManager->handle();
        } else {
            $job = (new FlightIdentifier($events, $this->user, $this->flag))->onQueue('flight-identify');
            dispatch($job);
        }
    }
}