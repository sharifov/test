<?php

namespace common\components;

use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\local\FlightSegment;
use common\models\Quote;
use src\dto\searchService\SearchServiceQuoteDTO;
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
    public const GDS_NDC_PROXY     = 'Y';
    public const GDS_AMADEUS_IGNORE_AVAIL = 'B';
    public const GDS_AWARD_TKTS    = 'R';

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
        self::GDS_NDC_PROXY => 'Ndc Proxy',
        self::GDS_AWARD_TKTS => 'Award TKTS',
    ];

    public const
        CABIN_ECONOMY = 'Y',
        CABIN_PREMIUM_ECONOMY = 'S',
        CABIN_BUSINESS = 'C',
        CABIN_PREMIUM_BUSINESS = 'J',
        CABIN_FIRST = 'F',
        CABIN_PREMIUM_FIRST = 'P';

    public const CABIN_LIST = [
        self::CABIN_ECONOMY => 'Economy',
        self::CABIN_PREMIUM_ECONOMY => 'Premium Economy',
        self::CABIN_BUSINESS => 'Business',
        self::CABIN_PREMIUM_BUSINESS => 'Premium Business',
        self::CABIN_FIRST => 'First',
        self::CABIN_PREMIUM_FIRST => 'Premium First',
    ];

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

    public static function getGDSKeyByName(string $name, bool $strict = false): ?string
    {
        if ($gdsSearch = array_search($name, self::GDS_LIST, $strict)) {
            return $gdsSearch;
        }
        return null;
    }

    /**
     * @param string|null $cabin
     * @param bool $isBasic
     * @return string|null
     */
    public static function getCabin(?string $cabin, bool $isBasic = false): ?string
    {
        return ($isBasic ? 'Basic ' : '') . (self::CABIN_LIST[$cabin] ?? $cabin);
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
     * @param SearchServiceQuoteDTO $dto
     * @return mixed
     * @throws \yii\httpclient\Exception
     */
    public static function getOnlineQuotes(SearchServiceQuoteDTO $dto)
    {
        $params = $dto->getAsArray();
        $response = \Yii::$app->airsearch->searchQuotes($params);

        if (!$result['data'] = $response['data']) {
            $result['error'] = $response['error'];
            \Yii::warning(
                [
                    'message' => $response['error'],
                    'lead_id' => $dto->getLeadId(),
                    'params' => $params
                ],
                'SearchService::getOnlineQuotes'
            );
        }
        return $result;
    }

    public static function getOnlineQuoteByKey(string $key)
    {
        $sid = \Yii::$app->params['search']['sid'];
        $response = \Yii::$app->airsearch->searchQuoteByKey($sid, $key);

        if (!$result['data'] = $response['data']) {
            $result['error'] = $response['error'];
            \Yii::error(
                [
                    'key' => $key,
                    'message' => $response['error']],
                'SearchService::getOnlineQuotesByKey'
            );
        }
        return $result;
    }

    /**
     * @param string $key
     * @param string $cid
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public static function getOnlineQuoteByKeySmartSearch(string $key, string $cid): array
    {
        $cid = !empty($cid) ? $cid : 'SAL103';
        $response = \Yii::$app->airsearch->searchQuoteByKey($cid, $key);

        if (!$result['data'] = $response['data']) {
            $result['error'] = $response['error'];
            \Yii::error(
                [
                    'key' => $key,
                    'message' => $response['error']],
                'SearchService::getOnlineQuotesByKeySmartSearch'
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

        return $interval->format('%dd %hh %im');
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
