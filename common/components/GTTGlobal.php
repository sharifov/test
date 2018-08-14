<?php

namespace common\components;

use common\models\Lead;
use common\models\local\FlightSegment;
use common\models\Quote;
use Yii;

class GTTGlobal
{
    const
        GDS_SABRE = 'S',
        GDS_AMADEUS = 'A',
        GDS_WORLDSPAN = 'W';

    public static function getGDSName($gds = null)
    {
        $mapping = [
            self::GDS_SABRE => 'Sabre',
            self::GDS_AMADEUS => 'Amadeus',
            self::GDS_WORLDSPAN => 'Worldspan'
        ];

        if ($gds === null) {
            return $mapping;
        }

        return isset($mapping[$gds]) ? $mapping[$gds] : $gds;
    }

    public static function getOnlineQuotes(Lead $lead, $gdsCode)
    {
        $returned = '';
        if ($lead->trip_type == Lead::TYPE_ROUND_TRIP) {
            $returned = date('m/d/Y', strtotime($lead->leadFlightSegments[0]->departure));
        }

        $fields = http_build_query([
            'departureAirport' => $lead->leadFlightSegments[0]->origin,
            'destinationAirport' => $lead->leadFlightSegments[0]->destination,
            'departureDate' => date('m/d/Y', strtotime($lead->leadFlightSegments[0]->departure)),
            'returnDate' => $returned,
            'type' => self::getType($lead->trip_type),
            'cabin' => $lead->cabin,
            'adults' => $lead->adults,
            'children' => $lead->children,
            'gds' => $gdsCode,
            'cid' => 'A7D1S1',
            'customerSessionId' => mt_rand(10000, 99999)
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, sprintf('http://50.97.0.109:7003/airticket/v1/mlist.aspx?%s', $fields));
        $result = curl_exec($ch);

        Yii::warning(sprintf("Request:\n%s\n\nDump:\n%s",
            print_r($fields, true),
            print_r(curl_getinfo($ch), true)
        ), 'GTTGlobal::getOnlineQuotes()');

        return json_decode($result, true);
    }

    protected static function getType($type)
    {
        $mapping = [
            Lead::TYPE_ROUND_TRIP => 'roundtrip',
            Lead::TYPE_ONE_WAY => 'oneway',
            Lead::TYPE_MULTI_DESTINATION => 'openjaw'
        ];
        return $mapping[$type];
    }

    public static function getItineraryDump($trips)
    {
        $segments = [];
        foreach ($trips as $trip) {
            foreach ($trip['segments'] as $segment) {
                $fSegment = new FlightSegment();
                $fSegment->departureTime = $segment['departureTime'];
                $fSegment->arrivalTime = $segment['arrivalTime'];
                $fSegment->airlineCode = $segment['airlineCode'];
                $fSegment->flightNumber = $segment['flightNumber'];
                $fSegment->bookingClass = $segment['BookingClass'];
                $fSegment->departureAirportCode = $segment['departureAirportCode'];
                $fSegment->destinationAirportCode = $segment['arrivalAirportCode'];
                $fSegment->destinationAirportCode = $segment['arrivalAirportCode'];
                $segments[] = $fSegment;
            }
        }
        return implode("\n", Quote::createDump($segments));
    }
}