<?php
namespace common\components;

use common\models\Lead;
use common\models\local\FlightSegment;
use common\models\Quote;
use Yii;
use common\models\Airline;
use common\models\Airport;

class SearchService
{

    const GDS_SABRE = 'S', GDS_AMADEUS = 'A', GDS_TRAVELPORT = 'W';
    const CABIN_ECONOMY = 'Y', CABIN_PREMIUM_ECONOMY = 'S', CABIN_BUSINESS = 'C',
    CABIN_PREMIUM_BUSINESS = 'J', CABIN_FIRST = 'F', CABIN_PREMIUM_FIRST = 'P';

    public static function durationInMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        if ($hours > 0)
            return $hours . 'h ' . $minutes . 'm';
            return $minutes . 'm';
    }

    public static function getGDSName($gds = null)
    {
        $mapping = [
            self::GDS_SABRE => 'Sabre',
            self::GDS_AMADEUS => 'Amadeus',
            self::GDS_TRAVELPORT => 'Travelport'
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

    public static function getOnlineQuotes(Lead $lead, $gdsCode)
    {
        $returned = '';
        if ($lead->trip_type == Lead::TRIP_TYPE_ROUND_TRIP) {
            $returned = date('m/d/Y', strtotime($lead->leadFlightSegments[1]->departure));
        }

        $fl = [];

        $params = [
            'cabin' => $lead->cabin,
            'gds' => $gdsCode,
            'cid' => 'SAL101',
            'adt' => $lead->adults,
            'chd' => $lead->children,
            'inf' => $lead->infants,
        ];

        foreach ($lead->leadFlightSegments as $flightSegment) {
            $fl[] = [
                'o' => $flightSegment->origin,
                'd' => $flightSegment->destination,
                'dt' => $flightSegment->departure
            ];
        }

        $params['fl'] = $fl;

        $fields = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'http://airsearch.api.travelinsides.com/v1/search?' . $fields);
        $result = curl_exec($ch);

        Yii::warning(sprintf("Request:\n%s\n\nDump:\n%s", print_r($fields, true), print_r(curl_getinfo($ch), true)), 'SearchService::getOnlineQuotes()');

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
}