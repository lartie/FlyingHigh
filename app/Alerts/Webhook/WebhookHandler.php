<?php

namespace App\Alerts\Webhook;

use App\Alerts\Response;
use LArtie\FlightStatsApi\Core\AlertMessages\CallbackEvents;
use LArtie\FlightStatsApi\Core\Objects\StatusFlight;
use App\FlightStatus;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;

/**
 * Class WebhookHandler
 * @package App\Alerts\Webhook
 */
final class WebhookHandler
{
    /**
     * @var CallbackEvents
     */
    private $callbackEvents;

    /**
     * @var StatusFlight
     */
    private $fsFlight;

    /**
     * @var FlightStatus
     */
    private $modelFlight;

    /**
     * WebhookHandler constructor.
     *
     * @param CallbackEvents $callbackEvents
     * @param StatusFlight $fsFlight
     * @param FlightStatus $modelFlight
     * @throws TelegramSDKException
     */
    public function __construct(CallbackEvents $callbackEvents, StatusFlight $fsFlight, FlightStatus $modelFlight)
    {
        $this->callbackEvents = $callbackEvents;
        $this->fsFlight = $fsFlight;
        $this->modelFlight = $modelFlight;
    }

    /**
     * Идентифицирует событие, собирает необходимые данные о полете и передает команду на рассылку сообщений пользователям,
     * которые подписались на уведомление конкретного авиаперелета
     *
     * @throws Throwable
     */
    public function handle()
    {
        $event = WebhookRepository::isAvailableEvent($this->callbackEvents->getType());

        if (empty($event)) {
            return;
        }
        $this->updateFlightInfo();

        $response = new Response($this->modelFlight, $this->fsFlight->getFlightStatusUpdates());
        $response->handle($event);
    }

    private function updateFlightInfo()
    {
        $this->modelFlight->flight_id = $this->fsFlight->getFlightId();
        $this->modelFlight->status = $this->fsFlight->getStatus();
        $this->modelFlight->arrival_gate = $this->fsFlight->getAirportResources()->arrivalGate ?? null;
        $this->modelFlight->departure_gate = $this->fsFlight->getAirportResources()->departureGate ?? null;
        $this->modelFlight->save();
    }
}