<?php

namespace App\Alerts\Webhook;

use Carbon\Carbon;
use LArtie\FlightStatsApi\Core\Methods\Alerts;
use LArtie\FlightStatsApi\Core\Objects\ScheduledFlight;

/**
 * Class WebhookRepository
 * @package App\Alerts\Webhook
 */
final class WebhookRepository
{
    /**
     * @param ScheduledFlight $schedule
     * @param Carbon $departureTime
     * @return mixed|string
     */
    public static function createRule(ScheduledFlight $schedule, Carbon $departureTime)
    {
        $alert = new Alerts();

        return $alert->createFlightRuleByDeparture(
            $schedule->getCarrierFsCode(),
            $schedule->getFlightNumber(),
            $schedule->getDepartureAirportFsCode(),
            $departureTime->year,
            $departureTime->month,
            $departureTime->day,
            self::getUrl(),
            'json',
            [
                'events' => self::getAvailableEventsForRule(),
            ]
        );
    }

    /**
     * @return string
     */
    public static function getAvailableEventsForRule() : string
    {
        $events = self::getAvailableEvents();

        return implode(',', array_pluck($events, 'short_name'));
    }

    /**
     * @return string
     * @todo
     */
    public static function getUrl() : string
    {
        return route('telegram.flyinghighbot.flightstats.webhook');
    }

    /**
     * @return array
     */
    public static function getAvailableEvents() : array
    {
        return config('flyinghigh.webhook.events.available', []);
    }

    /**
     * @param string $type
     * @return array
     */
    public static function isAvailableEvent(string $type) : array
    {
        $availableEvents = self::getAvailableEvents();

        foreach ($availableEvents as $event) {

            if ($event['full_name'] === $type) {
                return $event;
            }
        }
        return [];
    }

    /**
     * @return boolean
     */
    public static function isActive() : bool
    {
        return config('flyinghigh.webhook.active', false);
    }
}