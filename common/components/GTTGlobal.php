<?php

namespace common\components;

use common\models\Lead;
use common\models\local\FlightSegment;
use common\models\Quote;
use Yii;

class GTTGlobal
{
    public static function getGDSName($gds = null)
    {
        if ($gds === null) {
            return SearchService::GDS_LIST;
        }
        return SearchService::GDS_LIST[$gds] ?? $gds;
    }

    public static function getOnlineQuotes(Lead $lead, $gdsCode)
    {
        $returned = '';
        if ($lead->trip_type == Lead::TRIP_TYPE_ROUND_TRIP) {
            $returned = date('m/d/Y', strtotime($lead->leadFlightSegments[1]->departure));
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

        Yii::warning(sprintf(
            "Request:\n%s\n\nDump:\n%s",
            print_r($fields, true),
            print_r(curl_getinfo($ch), true)
        ), 'GTTGlobal::getOnlineQuotes()');

        return json_decode($result, true);
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
                if (!empty($segment['codeShare'])) {
                    $fSegment->operationAirlineCode = $segment['codeShare'];
                }
                $segments[] = $fSegment;
            }
        }
        return implode("\n", Quote::createDump($segments));
    }
}
