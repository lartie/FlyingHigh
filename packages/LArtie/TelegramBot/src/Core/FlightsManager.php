<?php

namespace LArtie\TelegramBot\Core;

use Carbon\Carbon;
use LArtie\Airports\Models\City;
use App\User;
use App\Repositories\AirportRepository;
use App\Repositories\EventRepository;
use LArtie\Google\Models\Event;

/**
 * Class FlightsManager
 * @package LArtie\TelegramBot\Core
 */
final class FlightsManager
{
    /**
     * @var integer
     */
    private $limit;

    /**
     * @var int
     */
    private $maxButtons;

    /**
     * @var int
     */
    private $firstPage = 1;

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $selectedPage;

    /**
     * @var int
     */
    private $flag;

    /**
     * @param int $flag
     */
    public function setFlag(int $flag)
    {
        $this->flag = $flag;
    }

    /**
     * FlightsManager constructor.
     *
     * @param User $user
     * @param int $selectedPage
     * @param int $limit
     * @param int $maxButtons
     */
    public function __construct(User $user, int $selectedPage, int $limit = 3, int $maxButtons = 5)
    {
        $this->user = $user;
        $this->limit = $limit;
        $this->selectedPage = $selectedPage;
        $this->maxButtons = $maxButtons;
    }

    /**
     * @param array $flights
     * @return array
     */
    public function paginate(array $flights = []) : array
    {
        if (empty($flights)) {
            $flights = $this->getFlights();
        }

        $this->sort($flights);

        $offset = ($this->limit * $this->selectedPage) - $this->limit;

        return [
            'items' => array_slice($flights, $offset, $this->limit),
            'keyboard' => $this->generateKeyboard(count($flights)),
        ];
    }

    /**
     * @param int $count
     * @return array
     */
    private function generateKeyboard(int $count) : array
    {
        $buttons = [];

        $pages = ceil($count/$this->limit);

        if ($pages == $this->firstPage) {
            return $buttons;
        }
        if ($pages > $this->maxButtons) {

            $buttons[] = $this->generateButton($this->firstPage);

            if ($this->selectedPage == 1) {
                $from = 2;
                $to = 5;
            } else if ($this->selectedPage == $pages) {
                $from = $pages - 3;
                $to = $pages;
            } else {
                if (($this->selectedPage + 3) > $pages) {
                    $from = $pages - 3;
                    $to = $pages;
                } else if (($this->selectedPage - 2) < $this->firstPage) {
                    $from = $this->selectedPage;
                    $to = $this->selectedPage + 3;
                } else {
                    $from = $this->selectedPage - 1;
                    $to = $this->selectedPage + 2;
                }
            }
            for ($i = $from; $i < $to; $i++) {
                $buttons[] = $this->generateButton($i);
            }

            $buttons[] = $this->generateButton($pages);
        } else {
            for ($i = 0; $i < $pages; $i++) {

                $nextPage = $i + 1;
                $buttons[] = $this->generateButton($nextPage);
            }
        }

        return [
            'reply_markup' => \GuzzleHttp\json_encode([
                'inline_keyboard' => [
                    $buttons,
                ],
            ]),
        ];
    }

    /**
     * @param int $nextPage
     * @return array
     */
    private function generateButton(int $nextPage) : array
    {
        $label = "$nextPage";
        $callbackData = $this->generateCallbackData($nextPage);

        if ($nextPage === $this->selectedPage) {
            $label = "« $label »";
        }

        return [
            'text' => $label,
            'callback_data' => $callbackData,
        ];
    }

    /**
     * @return array
     */
    private function getFlights() : array
    {
        $identifiedFlights = $this->getIdentifiedFlights();
        $unidentifiedFlights = $this->getUnidentifiedFlights();

        return array_merge($identifiedFlights, $unidentifiedFlights);
    }

    /**
     * @param $flights
     */
    private function sort(array &$flights)
    {
        usort($flights, function ($a, $b) {
            return strcmp($a['departure']['date'], $b['departure']['date']);
        });
    }

    /**
     * @return array
     */
    private function getUnidentifiedFlights() : array
    {
        /** @var Event[] $events */
        $events = $this->user->googleAccount->calendar->event()->where('identified', false)->get();

        $data = [];

        foreach ($events as $event) {

            $prepareEvent = EventRepository::getPrepareEventByEventID($event->id)[0] ?? null;

            if ($prepareEvent !== null) {

                $arrivalCity = City::where('name_ru', $prepareEvent->to)->first();

                $data[] = [
                    'identified' => false,
                    'departure' => [
                        'city' => $prepareEvent->name_en,
                        'date' => Carbon::parse($prepareEvent->start)->setTimezone($prepareEvent->start_timezone),
                    ],
                    'arrival' => [
                        'city' => $arrivalCity->name_en ?? $prepareEvent->to,
                        'date' => Carbon::parse($prepareEvent->end)->setTimezone($prepareEvent->end_timezone),
                    ],
                ];
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    private function getIdentifiedFlights() : array
    {
        $now = Carbon::now($this->user->googleAccount->calendar->timezone);

        $flights = $this->user->flights()->where('departure_date_local', '>', $now)->orderBy('departure_date_local', 'asc')->get();

        $data = [];

        foreach ($flights as $flight) {

            $depAirport = AirportRepository::getByIataCode($flight->departure_iata, ['*'], true);
            $arrAirport = AirportRepository::getByIataCode($flight->arrival_iata, ['*'], true);

            $data[] = [
                'identified' => true,
                'departure' => [
                    'city' => $depAirport->city->name_en,
                    'iata' => $flight->departure_iata,
                    'gate' => $flight->departure_gate,
                    'terminal' => $flight->departure_terminal,
                    'date' => $flight->departure_date_local,
                ],
                'arrival' => [
                    'city' => $arrAirport->city->name_en,
                    'iata' => $flight->arrival_iata,
                    'gate' => $flight->arrival_gate,
                    'terminal' => $flight->arrival_terminal,
                    'date' => $flight->arrival_date_local,
                ],
            ];
        }
        return $data;
    }

    /**
     * @param int $nextPage
     * @return string
     */
    private function generateCallbackData(int $nextPage) : string
    {
        $command = "flights/list?currentPage=$this->selectedPage&nextPage=$nextPage";

        if ($this->flag !== null) {
            $command .= "&flag=$this->flag";
        }
        return $command;
    }
}
