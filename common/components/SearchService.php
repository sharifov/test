<?php
namespace common\components;

use common\models\Lead;
use common\models\local\FlightSegment;
use common\models\Quote;
use Yii;
use common\models\Airline;
use common\models\Airport;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\helpers\VarDumper;

class SearchService
{

    public const GDS_SABRE         = 'S';
    public const GDS_AMADEUS       = 'A';
    public const GDS_TRAVELPORT    = 'T';
    public const GDS_AIRWANDER     = 'G';
    public const GDS_TRAVELFUSION  = 'F';
    public const GDS_COCKPIT       = 'C';
    public const GDS_ONEPOINT      = 'M';


    public const CABIN_ECONOMY = 'Y', CABIN_PREMIUM_ECONOMY = 'S', CABIN_BUSINESS = 'C',
    CABIN_PREMIUM_BUSINESS = 'J', CABIN_FIRST = 'F', CABIN_PREMIUM_FIRST = 'P';

    /**
     * @param $minutes
     * @return string
     */
    public static function durationInMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes %= 60;
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * @param null $gds
     * @return array|mixed|null
     */
    public static function getGDSName($gds = null)
    {
        $mapping = [
            self::GDS_SABRE         => 'Sabre',
            self::GDS_AMADEUS       => 'Amadeus',
            self::GDS_TRAVELPORT    => 'TravelPort',
            self::GDS_AIRWANDER     => 'Combined',
            self::GDS_TRAVELFUSION  => 'TravelFusion',
            self::GDS_COCKPIT       => 'Cockpit',
            self::GDS_ONEPOINT      => 'OnePoint',
        ];

        if ($gds === null) {
            return $mapping;
        }

        return isset($mapping[$gds]) ? $mapping[$gds] : $gds;
    }

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

        return isset($mapping[$cabin]) ? $mapping[$cabin] : $cabin;
    }

    public static function getCabinRealCode($cabin)
    {
        $mapping = [
            Lead::CABIN_ECONOMY => self::CABIN_ECONOMY,
            Lead::CABIN_PREMIUM => self::CABIN_PREMIUM_ECONOMY,
            Lead::CABIN_BUSINESS => self::CABIN_BUSINESS ,
            Lead::CABIN_FIRST => self::CABIN_FIRST,
        ];

        return isset($mapping[$cabin]) ? $mapping[$cabin] : $cabin;
    }

    public static function getOnlineQuotes(Lead $lead, $gdsCode = null)
    {
        $result = null;
        $returned = '';
        if ($lead->trip_type == Lead::TRIP_TYPE_ROUND_TRIP) {
            $returned = date('m/d/Y', strtotime($lead->leadFlightSegments[1]->departure));
        }

        $fl = [];

        $params = [
            'cabin' => self::getCabinRealCode($lead->cabin),
            'cid' => 'SAL101',
            'adt' => $lead->adults,
            'chd' => $lead->children,
            'inf' => $lead->infants,
        ];

        if($gdsCode) {
            $params['gds'] = $gdsCode;
        }

        foreach ($lead->leadFlightSegments as $flightSegment) {
            $fl[] = [
                'o' => $flightSegment->origin,
                'd' => $flightSegment->destination,
                'dt' => $flightSegment->departure
            ];
        }

        $params['fl'] = $fl;

        $fields = http_build_query($params);
        $url = \Yii::$app->params['searchApiUrl'].'?' . $fields;

        ///
        $client = new Client();
        $client->setTransport(CurlTransport::class);
        $request = $client->createRequest();
        $request->setMethod('GET')->setUrl($url)->setOptions([CURLOPT_ENCODING => 'gzip']);
        $response = $request->send();

        //VarDumper::dump($fields)

        // Yii::info($url, 'info\CURL:getOnlineQuotes:quickSearch');

        if ($response->isOk) {
            return $response->data;
        } else {
            \Yii::error(VarDumper::dumpAsString($response->content, 10), 'SearchService::getOnlineQuotes');
        }

        ///

        /* $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch); */

        //Yii::warning(sprintf("Request:\n%s\n\nDump:\n%s", print_r($fields, true), print_r(curl_getinfo($ch), true)), 'SearchService::getOnlineQuotes()');

        return json_decode($result, true);
    }

    public static function getAirlineLocationInfo($result)
    {
        $airlinesIata = [];
        $locationsIata = [];
        if(isset($result['results'])){
            foreach ($result['results'] as $resItem){
                if(!in_array($resItem['validatingCarrier'], $airlinesIata)){
                    $airlinesIata[] = $resItem['validatingCarrier'];
                }
                foreach ($resItem['trips'] as $trip){
                    foreach ($trip['segments'] as $segment){
                        if(!in_array($segment['operatingAirline'], $airlinesIata)){
                            $airlinesIata[] = $segment['operatingAirline'];
                        }
                        if(!in_array($segment['marketingAirline'], $airlinesIata)){
                            $airlinesIata[] = $segment['marketingAirline'];
                        }
                        if(!in_array($segment['departureAirportCode'], $locationsIata)){
                            $locationsIata[] = $segment['departureAirportCode'];
                        }
                        if(!in_array($segment['arrivalAirportCode'], $locationsIata)){
                            $locationsIata[] = $segment['arrivalAirportCode'];
                        }
                    }
                }
            }
        }

        $airlines = Airline::getAirlinesListByIata($airlinesIata);
        $locations = Airport::getAirportListByIata($locationsIata);

        return ['airlines' => $airlines, 'locations' => $locations];
    }

    public static function getAirlineLocationItem($resItem)
    {
        $airlinesIata = [];
        $locationsIata = [];


        if(!in_array($resItem['validatingCarrier'], $airlinesIata)){
            $airlinesIata[] = $resItem['validatingCarrier'];
        }
        foreach ($resItem['trips'] as $trip){
            foreach ($trip['segments'] as $segment){
                if(!in_array($segment['operatingAirline'], $airlinesIata)){
                    $airlinesIata[] = $segment['operatingAirline'];
                }
                if(!in_array($segment['marketingAirline'], $airlinesIata)){
                    $airlinesIata[] = $segment['marketingAirline'];
                }
                if(!in_array($segment['departureAirportCode'], $locationsIata)){
                    $locationsIata[] = $segment['departureAirportCode'];
                }
                if(!in_array($segment['arrivalAirportCode'], $locationsIata)){
                    $locationsIata[] = $segment['arrivalAirportCode'];
                }
            }
        }

        $airlines = Airline::getAirlinesListByIata($airlinesIata);
        $locations = Airport::getAirportListByIata($locationsIata);

        return ['airlines' => $airlines, 'locations' => $locations];
    }

    public static function getItineraryDump($result)
    {
        $segments = [];

        foreach ($result['trips'] as $trip){
            foreach ($trip['segments'] as $segment) {
                $fSegment = new FlightSegment();
                $fSegment->departureTime = $segment['departureTime'];
                $fSegment->arrivalTime = $segment['arrivalTime'];
                $fSegment->airlineCode = $segment['marketingAirline'];
                $fSegment->flightNumber = $segment['flightNumber'];
                $fSegment->bookingClass = $segment['bookingClass'];
                $fSegment->departureAirportCode = $segment['departureAirportCode'];
                $fSegment->destinationAirportCode = $segment['arrivalAirportCode'];
                if($segment['operatingAirline'] != $segment['marketingAirline']){
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
     * @param string $projectName
     * @return string
     */
    public static function getRecheckBaggageText(string $countryName = '', string $projectName = ''): string
    {
        $str = "This unique itinerary cannot be found elsewhere.\n
The connection in " . $countryName . " is not provided by the airlines. You will need to leave the visa-free transit zone and enter " . $countryName . " to check in for your next flight — passing through security and a visa check at immigration.\n
The layover time is long enough for the transfer and it's protected by the " . $projectName . ' Guarantee in case of any delay.';
        return $str;
    }
}