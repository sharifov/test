<?php

namespace sales\services\parsingDump;

use common\models\Airline;
use common\models\Airport;
use DateTime;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use sales\services\parsingDump\lib\ParsingDump;
use yii\helpers\VarDumper;

/**
 * Class ReservationService
 */
class ReservationService
{
    public string $gds;

    /**
     * ReservationService constructor.
     * @param string $gds
     */
    public function __construct(string $gds)
	{
		$this->gds = ParsingDump::setGdsForParsing($gds);
	}

    /**
     * @param $string
     * @param bool $validation
     * @param array $itinerary`
     * @param bool $onView
     * @return array
     * @throws \Exception
     */
    public function parseReservation($string, $validation = true, &$itinerary = [], $onView = false): array
    {
        $i = 0;
        $result = [];
        $parserReservation = ParsingDump::initClass($this->gds, ParsingDump::PARSING_TYPE_RESERVATION);
        $string = trim($string);
        $rows = explode("\n", $string);
        if (!empty($itinerary) && $validation) {
            $itinerary = [];
        }

        foreach ($rows as $key => $row) {
            try {
                if (empty($rawData = $parserReservation->parseRow($row))) {
                    continue;
                }
                $parseData = $parserReservation->processingRow($rawData);

                $result[$i]['carrier'] = $parseData['airline'];
                $result[$i]['airlineName'] = $this->getAirlineName($parseData['airline'], $onView);
                $result[$i]['departureAirport'] = $parseData['departure_airport_iata'];
                $result[$i]['arrivalAirport'] = $parseData['arrival_airport_iata'];
                $result[$i]['departureDateTime'] = $parseData['departure_date_time'];
                $result[$i]['arrivalDateTime'] = $parseData['arrival_date_time'];
                $result[$i]['flightNumber'] = $parseData['flight_number'];
                $result[$i]['bookingClass'] = $parseData['booking_class'];
                $result[$i]['departureCity'] = Airport::findIdentity($parseData['departure_airport_iata']);
                $result[$i]['arrivalCity'] = Airport::findIdentity($parseData['arrival_airport_iata']);
                $result[$i]['flightDuration'] = $this->getFlightDuration(
                    $result[$i]['departureDateTime'],
                    $result[$i]['arrivalDateTime'],
                    $result[$i]['departureCity'],
                    $result[$i]['arrivalCity']
                );
                $result[$i]['layoverDuration'] = $this->getLayoverDuration($result, $i);

                if ($airline = Airline::findIdentity($parseData['airline'])) {
                    $result[$i]['cabin'] = $airline->getCabinByClass($parseData['booking_class']);
                }

                $itinerary[] = (new ItineraryDumpDTO([]))->feelByParsedReservationDump($result[$i]);
                $i ++;

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