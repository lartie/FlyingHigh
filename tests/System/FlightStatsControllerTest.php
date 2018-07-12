<?php

use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;

class FlightStatsControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testWebhook()
    {
        $client = new Client([
//            'base_uri' => $this->baseUrl,
        ]);

        $uri = route('telegram.flyinghighbot.flightstats.webhook', [], true);

        $params = $this->makeAlert($this->baseUrl . $uri);

        $reponse = $client->post($uri, [
            'content-type' => 'application/json',
            'body' => $params
        ]);
    }

    /**
     * @param $uri
     * @param string $type
     * @return string
     */
    private function makeAlert($uri, $type = 'CANCELLED')
    {
        $alert = '{
            "alert": {
                "event": {
                    "type": "' . $type . '"
                },
                "dataSource": "Simulated Event Source",
                "dateTimeRecorded": "2016-09-13T09:39:25.752Z",
                "rule": {
                    "id": "1",
                    "name": "name",
                    "description": "description",
                    "carrierFsCode": "AA",
                    "flightNumber": "100",
                    "departureAirportFsCode": "JFK",
                    "arrivalAirportFsCode": "LHR",
                    "departure": "2016-09-13T05:39:25.752",
                    "arrival": "2016-09-13T13:39:25.752",
                    "ruleEvents": [{
                        "type": "ALL_CHANGES"
                    }],
                    "nameValues": [],
                    "delivery": {
                        "format": "json",
                        "destination": "' . $uri . '"
                    }
                },
                "flightStatus": {
                    "flightId": 1,
                    "carrierFsCode": "AA",
                    "flightNumber": "100",
                    "departureAirportFsCode": "JFK",
                    "arrivalAirportFsCode": "LHR",
                    "departureDate": {
                        "dateLocal": "2016-09-13T05:39:25.000",
                        "dateUtc": "2016-09-13T05:39:25.000Z"
                    },
                    "arrivalDate": {
                        "dateLocal": "2016-09-13T13:39:25.000",
                        "dateUtc": "2016-09-13T13:39:25.000Z"
                    },
                    "status": "A",
                    "operationalTimes": {
                        "publishedDeparture": {
                            "dateLocal": "2016-09-13T05:39:25.000",
                            "dateUtc": "2016-09-13T05:39:25.000Z"
                        },
                        "publishedArrival": {
                            "dateLocal": "2016-09-13T13:39:25.000",
                            "dateUtc": "2016-09-13T13:39:25.000Z"
                        },
                        "estimatedGateDeparture": {
                            "dateLocal": "2016-09-13T05:39:25.000",
                            "dateUtc": "2016-09-13T05:39:25.000Z"
                        },
                        "actualGateDeparture": {
                            "dateLocal": "2016-09-13T05:39:25.000",
                            "dateUtc": "2016-09-13T05:39:25.000Z"
                        },
                        "estimatedGateArrival": {
                            "dateLocal": "2016-09-13T13:39:25.000",
                            "dateUtc": "2016-09-13T13:39:25.000Z"
                        },
                        "actualGateArrival": {
                            "dateLocal": "2016-09-13T13:39:25.000",
                            "dateUtc": "2016-09-13T13:39:25.000Z"
                        }
                    },
                    "flightDurations": {
                        "blockMinutes": 480
                    },
                    "flightStatusUpdates": [{
                        "updatedAt": {
                            "dateUtc": "2016-09-13T09:39:25.762Z"
                        },
                        "source": "Simulated Event Source",
                        "updatedTextFields": [{
                            "field": "STS",
                            "originalText": "S",
                            "newText": "A"
                        }],
                        "updatedDateFields": []
                    }],
                    "operatingCarrierFsCode": "AA",
                    "primaryCarrierFsCode": "AA"
                }
            },
            "appendix": {
                "airlines": [{
                    "fs": "AA",
                    "iata": "AA",
                    "icao": "AAL",
                    "name": "American Airlines",
                    "phoneNumber": "08457-567-567",
                    "active": true
                }],
                "airports": [{
                    "fs": "JFK",
                    "iata": "JFK",
                    "icao": "KJFK",
                    "faa": "JFK",
                    "name": "John F. Kennedy International Airport",
                    "street1": "JFK Airport",
                    "city": "New York",
                    "cityCode": "NYC",
                    "stateCode": "NY",
                    "postalCode": "11430",
                    "countryCode": "US",
                    "countryName": "United States",
                    "regionName": "North America",
                    "timeZoneRegionName": "America/New_York",
                    "weatherZone": "NYZ178",
                    "localTime": "2016-09-13T05:39:25.762",
                    "utcOffsetHours": -4,
                    "latitude": 40.642335,
                    "longitude": -73.78817,
                    "elevationFeet": 13,
                    "classification": 1,
                    "active": true
                }, {
                    "fs": "LHR",
                    "iata": "LHR",
                    "icao": "EGLL",
                    "name": "London Heathrow Airport",
                    "city": "London",
                    "cityCode": "LON",
                    "stateCode": "EN",
                    "countryCode": "GB",
                    "countryName": "United Kingdom",
                    "regionName": "Europe",
                    "timeZoneRegionName": "Europe/London",
                    "localTime": "2016-09-13T10:39:25.762",
                    "utcOffsetHours": 1,
                    "latitude": 51.469603,
                    "longitude": -0.453566,
                    "elevationFeet": 80,
                    "classification": 1,
                    "active": true
                }]
            }
        }';

        return $alert;
    }
}
