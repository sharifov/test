<?php

namespace sales\services\parsingDump;

use common\models\Airline;
use common\models\Airport;
use DateTime;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use sales\services\lead\calculator\LeadTripTypeCalculator;
use sales\services\lead\calculator\SegmentDTO;
use sales\services\parsingDump\lib\ParsingDump;
use yii\helpers\VarDumper;

/**
 * Class ReservationService
 */
class ReservationService
{
    public string $gds;
    public array $parseResult = [];
    public array $itinerary = [];
    public bool $parseStatus;

    /**
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

                $this->parseResult[$i]['carrier'] = $parseData['airline'];
                $this->parseResult[$i]['airlineName'] = $this->getAirlineName($parseData['airline'], $onView);
                $this->parseResult[$i]['departureAirport'] = $parseData['departure_airport_iata'];
                $this->parseResult[$i]['arrivalAirport'] = $parseData['arrival_airport_iata'];
                $this->parseResult[$i]['segmentIata'] = $parseData['departure_airport_iata'] . $parseData['arrival_airport_iata'];
                $this->parseResult[$i]['departureDateTime'] = $parseData['departure_date_time'];
                $this->parseResult[$i]['arrivalDateTime'] = $parseData['arrival_date_time'];
                $this->parseResult[$i]['flightNumber'] = $parseData['flight_number'];
                $this->parseResult[$i]['bookingClass'] = $parseData['booking_class'];
                $this->parseResult[$i]['departureCity'] = Airport::findIdentity($parseData['departure_airport_iata']);
                $this->parseResult[$i]['arrivalCity'] = Airport::findIdentity($parseData['arrival_airport_iata']);
                $this->parseResult[$i]['flightDuration'] = $this->getFlightDuration(
                    $this->parseResult[$i]['departureDateTime'],
                    $this->parseResult[$i]['arrivalDateTime'],
                    $this->parseResult[$i]['departureCity'],
                    $this->parseResult[$i]['arrivalCity']
                );
                $this->parseResult[$i]['layoverDuration'] = $this->getLayoverDuration($this->parseResult, $i);

                if ($airline = Airline::findIdentity($parseData['airline'])) {
                    $this->parseResult[$i]['cabin'] = $airline->getCabinByClass($parseData['booking_class']);
                }

                $itinerary[] = $this->itinerary[] = (new ItineraryDumpDTO([]))
                    ->feelByParsedReservationDump($this->parseResult[$i]);

                $i ++;
            } catch (\Throwable $throwable) {
                \Yii::error(VarDumper::dumpAsString([
                     'parseData' => $parseData,
                     'throwable' => $throwable,
                ], 20),
                'ReservationService:parseReservation:Throwable');
                $this->parseResult['failed']['segment'][] = $row;
            }
        }

        $this->parseStatus = true;
        if ($validation && isset($this->parseResult['failed'])) {
            $this->parseResult = [];
            $this->parseStatus = false;
        }
        return $this->parseResult;
    }

    /**
     * @return string|null
     */
    public function getTripType(): ?string
    {
        if (empty($this->parseResult)) {
            return null;
        }
        $segmentsDTO = [];
        foreach ($this->parseResult as $segment) {
            $segmentsDTO[] = new SegmentDTO($segment['departureAirport'], $segment['arrivalAirport']);
        }
        return LeadTripTypeCalculator::calculate(...$segmentsDTO);
    }

    /**
     * @param DateTime $departureDateTime
     * @param DateTime $arrivalDateTime
     * @param Airport|null $departureCity
     * @param Airport|null $arrivalCity
     * @return float|int
     */
    private function getFlightDuration(DateTime $departureDateTime, DateTime $arrivalDateTime, ?Airport $departureCity, ?Airport $arrivalCity)
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
        if (isset($data[$index - 1])) {
            $result = ($data[$index]['departureDateTime']->getTimestamp() - $data[$index - 1]['arrivalDateTime']->getTimestamp()) / 60;
        }
        return $result ?? 0;
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