<?php

namespace sales\services\parsingDump;

use common\components\SearchService;
use common\models\Airline;
use common\models\Airport;
use DateTime;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use sales\services\parsingDump\lib\worldSpan\Reservation;
use yii\helpers\VarDumper;

/**
 * Class ReservationService
 */
class WorldSpanReservationService
{
    /**
     * @param $string
     * @param bool $validation
     * @param array $itinerary
     * @param bool $onView
     * @return array
     * @throws \Exception
     */
    public function parseReservation($string, $validation = true, &$itinerary = [], $onView = false): array
    {
        $result = [];
        $parserReservation = new Reservation();
        $string = trim($string);
        $rows = explode("\n", $string);
        if (!empty($itinerary) && $validation) {
            $itinerary = [];
        }

        foreach ($rows as $key => $row) {
            try {
                if (empty($rawData = $parserReservation->parseRow($row))) {
                    $result['failed']['parsed'][] = $row;
                    continue;
                }
                $parseData = $parserReservation->dataMapping($rawData);

                $result[$key]['carrier'] = $parseData['airline'];
                $result[$key]['airlineName'] = $this->getAirlineName($parseData['airline'], $onView);
                $result[$key]['departureAirport'] = $parseData['departure_airport_iata'];
                $result[$key]['arrivalAirport'] = $parseData['arrival_airport_iata'];
                $result[$key]['departureDateTime'] = $parserReservation->createDateTime(
                    $parseData['departure_date_day'],
                    $parseData['departure_date_month'],
                    $parseData['departure_time_hh'],
                    $parseData['departure_time_mm']
                );

                $result[$key]['arrivalDateTime'] = $parserReservation->getArrivalDateTime(
                    $result[$key]['departureDateTime'],
                    $parseData['arrival_time_hh'],
                    $parseData['arrival_time_mm'],
                    $parseData['arrival_offset']
                );
                $result[$key]['flightNumber'] = $parseData['flight_number'];
                $result[$key]['bookingClass'] = $parseData['booking_class'];
                $result[$key]['departureCity'] = Airport::findIdentity($parseData['departure_airport_iata']);
                $result[$key]['arrivalCity'] = Airport::findIdentity($parseData['arrival_airport_iata']);
                $result[$key]['flightDuration'] = $this->getFlightDuration(
                    $result[$key]['departureDateTime'],
                    $result[$key]['arrivalDateTime'],
                    $result[$key]['departureCity'],
                    $result[$key]['arrivalCity']
                );
                $result[$key]['layoverDuration'] = $this->getLayoverDuration($result, $key);
                $result[$key]['arrivalDayOffset'] = $parserReservation->prepareArrivalOffset($parseData['arrival_offset']);
                $result[$key]['cabin'] = SearchService::getCabinRealCode($parseData['cabin']);

                $itinerary[] = (new ItineraryDumpDTO([]))->feelByParsedReservationDump($result[$key]);

            } catch (\Throwable $throwable) {
                \Yii::error(VarDumper::dumpAsString([
                     'parseData' => $parseData,
                     'throwable' => $throwable,
                ], 10),
                'WorldSpanReservationService:parseReservation:Throwable');
                $result['failed']['segment'][] = $row;
            }
        }

        if ($validation && isset($result['failed'])) {
            $result = [];
        }

        return $result;
    }

    /** TODO:: to helper
     * @param DateTime $departureDateTime
     * @param DateTime $arrivalDateTime
     * @param Airport|null $departureCity
     * @param Airport|null $arrivalCity
     * @return float|int
     */
    public function getFlightDuration(DateTime $departureDateTime, DateTime $arrivalDateTime, ?Airport $departureCity, ?Airport $arrivalCity)
    {
        if (isset($departureCity, $arrivalCity) && $departureCity->dst !== $arrivalCity->dst) {
            $flightDuration = ($arrivalDateTime->getTimestamp() - $departureDateTime->getTimestamp()) / 60;
            $flightDuration = (int)$flightDuration + ((int)$departureCity->dst * 60) - ((int)$arrivalCity->dst * 60);
        } else {
            $flightDuration = ($arrivalDateTime->getTimestamp() - $departureDateTime->getTimestamp()) / 60;
        }
        return $flightDuration;
    }

    /**
     * @param array $data
     * @param int $index
     * @return float|int
     */
    private function getLayoverDuration(array $data, int $index)
    {
        $result = 0;
        if (isset($data[$index - 1])) {
            $result = ($data[$index]['departureDateTime']->getTimestamp() - $data[$index - 1]['arrivalDateTime']->getTimestamp()) / 60;
        }
        return $result;
    }

    /**
     * @param string $code
     * @param bool $onView
     * @return string
     */
    private function getAirlineName(string $code, bool $onView): string
    {
        $airline = (!$onView) ? Airline::findIdentity($code) : null;
        return $airline ? $airline->name : $code;
    }
}