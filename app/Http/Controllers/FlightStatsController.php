<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Alerts\Webhook\WebhookHandler;
use LArtie\FlightStatsApi\Core\AlertMessages\CallbackFields;
use App\Repositories\FlightStatusRepository;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Throwable;

/**
 * Class FlightStatsController
 * @package App\Http\Controllers
 */
final class FlightStatsController extends Controller
{
    /**
     * Данный метод принимает изменения состояния авиаперелетов
     * Подробнее: https://developer.flightstats.com/api-docs/alerts/v1
     *
     * @param Request $request
     * @throws TelegramSDKException
     * @throws Throwable
     */
    public function webhook(Request $request)
    {
        $alert = json_decode(json_encode($request->json('alert')));

        if ($alert !== null) {
            $callbackFields = new CallbackFields($alert);

            $flightStatsRepository = new FlightStatusRepository();

            $flight = $flightStatsRepository->getByIataCodesViaLocalTime([
                'departure_iata' => $callbackFields->getFlightStatus()->getDepartureAirportFsCode(),
                'arrival_iata' => $callbackFields->getFlightStatus()->getArrivalAirportFsCode(),
                'departure_date_local' => Carbon::parse($callbackFields->getFlightStatus()->getDepartureDate()->dateLocal),
                'arrival_date_local' => Carbon::parse($callbackFields->getFlightStatus()->getArrivalDate()->dateLocal),
            ]);

            if ($flight) {
                $alertHandler = new WebhookHandler($callbackFields->getEvent(), $callbackFields->getFlightStatus(), $flight);
                $alertHandler->handle();
            }
        }
    }
}
