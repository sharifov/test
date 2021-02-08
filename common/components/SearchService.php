<?php

namespace common\components;

use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\local\FlightSegment;
use common\models\Quote;
use Yii;
use common\models\Airline;
use common\models\Airports;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\helpers\VarDumper;

class SearchService
{
    public const GDS_SABRE         = 'S';
    public const GDS_AMADEUS       = 'A';
    public const GDS_WORLDSPAN     = 'W';
    public const GDS_APOLLO        = 'L';
    public const GDS_COCKPIT       = 'C';
    public const GDS_AIRWANDER     = 'G';
    public const GDS_TRAVELFUSION  = 'F';
    public const GDS_PKFARE        = 'P';
    public const GDS_TRAVELPORT    = 'T';
    public const GDS_ONEPOINT      = 'M';
    public const GDS_AMADEUS_IGNORE_AVAIL = 'B';

    public const GDS_LIST = [
        self::GDS_SABRE => 'Sabre',
        self::GDS_AMADEUS => 'Amadeus',
        self::GDS_WORLDSPAN => 'WorldSpan',
        self::GDS_TRAVELPORT => 'TravelPort',
        self::GDS_AIRWANDER => 'AirWander',
        self::GDS_TRAVELFUSION => 'TravelFusion',
        self::GDS_COCKPIT => 'Cockpit',
        self::GDS_ONEPOINT => 'OnePoint',
        self::GDS_APOLLO => 'Apollo',
        self::GDS_PKFARE => 'Pkfare',
        self::GDS_AMADEUS_IGNORE_AVAIL => 'Amadeus ignore avail',
    ];

    public const
        CABIN_ECONOMY = 'Y',
        CABIN_PREMIUM_ECONOMY = 'S',
        CABIN_BUSINESS = 'C',
        CABIN_PREMIUM_BUSINESS = 'J',
        CABIN_FIRST = 'F',
        CABIN_PREMIUM_FIRST = 'P';

    /**
     * @param $minutes
     * @return string
     */
    public static function durationInMinutes($minutes): string
    {
        $hours = floor($minutes / 60);
        $minutes %= 60;
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * @param string|null $gds
     * @return string|null
     */
    public static function getGDSName(?string $gds): ?string
    {
        return self::GDS_LIST[$gds] ?? $gds;
    }

    /**
     * @param null $cabin
     * @return array|mixed|null
     */
    public static function getCabin($cabin = null)
    {
        $mapping = [
            self::CABIN_ECONOMY => 'Economy',
            self::CABIN_PREMIUM_ECONOMY => 'Premium Economy',
            self::CABIN_BUSINESS => 'Business',
            self::CABIN_PREMIUM_BUSINESS => 'Premium Business',
            self::CABIN_FIRST => 'First',
            self::CABIN_PREMIUM_FIRST => 'Premium First',
        ];

        if ($cabin === null) {
            return $mapping;
        }

        return $mapping[$cabin] ?? $cabin;
    }

    /**
     * @param $cabin
     * @return mixed
     */
    public static function getCabinRealCode($cabin)
    {
        $mapping = [
            Lead::CABIN_ECONOMY => self::CABIN_ECONOMY,
            Lead::CABIN_PREMIUM => self::CABIN_PREMIUM_ECONOMY,
            Lead::CABIN_BUSINESS => self::CABIN_BUSINESS ,
            Lead::CABIN_FIRST => self::CABIN_FIRST,
        ];

        return $mapping[$cabin] ?? $cabin;
    }

    /**
     * @param string $flexType
     * @return int|null
     */
    public static function getSearchFlexType(string $flexType): ?int
    {
        $mapping = [
            LeadFlightSegment::FLEX_TYPE_MINUS          => -1,
            LeadFlightSegment::FLEX_TYPE_PLUS_MINUS     => 0,
            LeadFlightSegment::FLEX_TYPE_PLUS           => 1
        ];

        return $mapping[$flexType] ?: null;
    }

    /**
     * @param Lead $lead
     * @param int $limit
     * @param null $gdsCode
     * @param bool $group
     * @return mixed
     * @throws \yii\httpclient\Exception
     * @throws \JsonException
     */
    public static function getOnlineQuotes(Lead $lead, int $limit = 600, $gdsCode = null, bool $group = true)
    {
        $result = [];
        $fl = [];

        $params = [
            'cabin' => self::getCabinRealCode($lead->cabin),
            'cid' => 'SAL101',
            'adt' => $lead->adults,
            'chd' => $lead->children,
            'inf' => $lead->infants,
            'group' => $group,
        ];

        if ($limit) {
            $params['limit'] = $limit;
        }

        if ($gdsCode) {
            $params['gds'] = $gdsCode;
        }

        foreach ($lead->leadFlightSegments as $flightSegment) {
            $segment = [
                'o' => $flightSegment->origin,
                'd' => $flightSegment->destination,
                'dt' => $flightSegment->departure
            ];

            if ($flightSegment->flexibility > 0) {
                $segment['flex'] = $flightSegment->flexibility;

                if ($flightSegment->flexibility_type && $flexType = self::getSearchFlexType($flightSegment->flexibility_type)) {
                    $segment['ft'] = $flexType;
                }
            }

            $fl[] = $segment;
        }

        $params['fl'] = $fl;

        if ($lead->client->isExcluded()) {
            $params['ppn'] = $lead->client->cl_ppn;
        }

        $response = \Yii::$app->airsearch->sendRequest('v1/search', $params, 'GET');

        if ($response->isOk) {
            $result['data'] = $response->data;
        } else {
            $result['error'] = $response->content;
            \Yii::error(
                ['lead_id' => $lead->id,
                'params' => $params,
                'message' => $response->content],
                'SearchService::getOnlineQuotes'
            );
        }

        return $result;
    }

    /**
     * @param $result
     * @return array
     * @throws \Exception
     */
    public static function getAirlineLocationInfo($result): array
    {
        $airlinesIata = [];
        $locationsIata = [];
        if (is_array($results = ArrayHelper::getValue($result, 'results'))) {
            foreach ($results as $resItem) {
                $airlinesIata[] = ArrayHelper::getValue($resItem, 'validatingCarrier', '');
                if (is_array($trips = ArrayHelper::getValue($resItem, 'trips'))) {
                    foreach ($trips as $trip) {
                        if (is_array($segments = ArrayHelper::getValue($trip, 'segments'))) {
                            foreach ($segments as $segment) {
                                $airlinesIata[] = ArrayHelper::getValue($segment, 'operatingAirline', '');
                                $airlinesIata[] = ArrayHelper::getValue($segment, 'marketingAirline', '');
                                $locationsIata[] = ArrayHelper::getValue($segment, 'departureAirportCode', '');
                                $locationsIata[] = ArrayHelper::getValue($segment, 'arrivalAirportCode', '');
                            }
                        }
                    }
                }
            }
        }
        return [
            'airlines' => Airline::getAirlinesListByIata(array_unique($airlinesIata)),
            'locations' => Airports::getAirportListByIata(array_unique($locationsIata))
        ];
    }

    public static function getAirlineLocationItem($resItem)
    {
        $airlinesIata = [];
        $locationsIata = [];


        if (!in_array($resItem['validatingCarrier'], $airlinesIata)) {
            $airlinesIata[] = $resItem['validatingCarrier'];
        }
        foreach ($resItem['trips'] as $trip) {
            foreach ($trip['segments'] as $segment) {
                if (!in_array($segment['operatingAirline'], $airlinesIata)) {
                    $airlinesIata[] = $segment['operatingAirline'];
                }
                if (!in_array($segment['marketingAirline'], $airlinesIata)) {
                    $airlinesIata[] = $segment['marketingAirline'];
                }
                if (!in_array($segment['departureAirportCode'], $locationsIata)) {
                    $locationsIata[] = $segment['departureAirportCode'];
                }
                if (!in_array($segment['arrivalAirportCode'], $locationsIata)) {
                    $locationsIata[] = $segment['arrivalAirportCode'];
                }
            }
        }

        $airlines = Airline::getAirlinesListByIata($airlinesIata);
        $locations = Airports::getAirportListByIata($locationsIata);

        return ['airlines' => $airlines, 'locations' => $locations];
    }

    public static function getItineraryDump($result)
    {
        $segments = [];

        foreach ($result['trips'] as $trip) {
            foreach ($trip['segments'] as $segment) {
                $fSegment = new FlightSegment();
                $fSegment->departureTime = $segment['departureTime'];
                $fSegment->arrivalTime = $segment['arrivalTime'];
                $fSegment->airlineCode = $segment['marketingAirline'];
                $fSegment->flightNumber = $segment['flightNumber'];
                $fSegment->bookingClass = $segment['bookingClass'];
                $fSegment->departureAirportCode = $segment['departureAirportCode'];
                $fSegment->destinationAirportCode = $segment['arrivalAirportCode'];
                if ($segment['operatingAirline'] != $segment['marketingAirline']) {
                    $fSegment->operationAirlineCode = $segment['operatingAirline'];
                }
                $segments[] = $fSegment;
            }
        }
        return implode("\n", Quote::createDump($segments));
    }

    public static function getLayoverDuration($from, $to)
    {
        $fromDateTime = new \DateTime($from);
        $toDateTime = new \DateTime($to);
        $interval = $toDateTime->diff($fromDateTime);

        return $interval->format('%hh %im');
    }

    protected static function getType($type)
    {
        $mapping = [
            Lead::TRIP_TYPE_ROUND_TRIP => 'roundtrip',
            Lead::TRIP_TYPE_ONE_WAY => 'oneway',
            Lead::TRIP_TYPE_MULTI_DESTINATION => 'openjaw'
        ];
        return $mapping[$type];
    }

    /**
     * @param string $countryName
     * @return string
     */
    public static function getRecheckBaggageText(string $countryName = ''): string
    {
        $str = "This unique itinerary cannot be found elsewhere.\n
The connection in " . $countryName . " is not provided by the airlines. You will need to leave the visa-free transit zone and enter " . $countryName . " to check in for your next flight — passing through security and a visa check at immigration";
        // The layover time is long enough for the transfer and it's protected by the " . $projectName . ' Guarantee in case of any delay.';
        return $str;
    }
}
