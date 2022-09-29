<?php

namespace src\services\parsingDump;

use common\models\Airline;
use common\models\Airports;
use DateTime;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\helpers\FlightQuoteHelper;
use src\helpers\app\AppHelper;
use src\services\lead\calculator\LeadTripTypeCalculator;
use src\services\lead\calculator\SegmentDTO;
use src\services\parsingDump\lib\ParsingDump;
use yii\helpers\VarDumper;

/**
 * Class ReservationService
 *
 * @property string $gds
 * @property array $parseResult
 * @property array $itinerary
 * @property bool $parseStatus
 */
class ReservationService
{
    public string $gds;
    public array $parseResult = [];
    public array $itinerary = [];
    public ?bool $parseStatus;

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
        $rawData = [];
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
                $this->parseResult[$i]['airlineObj'] = $airline = Airline::findIdentity($parseData['airline']);
                $this->parseResult[$i]['airlineName'] = $airline->name ?? $parseData['airline'];
                $this->parseResult[$i]['departureAirport'] = $parseData['departure_airport_iata'];
                $this->parseResult[$i]['arrivalAirport'] = $parseData['arrival_airport_iata'];
                $this->parseResult[$i]['segmentIata'] = $parseData['departure_airport_iata'] . $parseData['arrival_airport_iata'];
                $this->parseResult[$i]['departureDateTime'] = $parseData['departure_date_time'];
                $this->parseResult[$i]['arrivalDateTime'] = $parseData['arrival_date_time'];
                $this->parseResult[$i]['flightNumber'] = $parseData['flight_number'];
                $this->parseResult[$i]['bookingClass'] = $parseData['booking_class'];
                $this->parseResult[$i]['departureCity'] = Airports::findByIata($parseData['departure_airport_iata']);
                $this->parseResult[$i]['arrivalCity'] = Airports::findByIata($parseData['arrival_airport_iata']);
                $this->parseResult[$i]['flightDuration'] = $this->getFlightDuration(
                    $this->parseResult[$i]['departureDateTime'],
                    $this->parseResult[$i]['arrivalDateTime'],
                    $this->parseResult[$i]['departureCity'],
                    $this->parseResult[$i]['arrivalCity']
                );
                if ($this->parseResult[$i]['flightDuration'] <= 0 && $validation) {
                    \Yii::warning('Negative or zero flight duration (' . $this->parseResult[$i]['flightDuration'] . ' sec) for dump row: ' . $row, 'ReservationService:parseReservation:flightDuration');
                    $this->parseResult[$i]['flightDuration'] = 0;
                }

                $this->parseResult[$i]['layoverDuration'] = $this->getLayoverDuration($this->parseResult, $i);
                if ($this->parseResult[$i]['layoverDuration'] < 0 && $validation) {
                    \Yii::warning('Negative layover duration (' . $this->parseResult[$i]['layoverDuration'] . ' sec) for dump row: ' . $row, 'ReservationService:parseReservation:layoverDuration');
                    $this->parseResult[$i]['layoverDuration'] = 0;
                }

                $this->parseResult[$i]['operatingAirline'] = $operatedCode = $parseData['operated'] ?? null;
                $this->parseResult[$i]['operatingAirlineObj'] = $operatingAirlineObj = ($operatedCode) ? $operatingAirline = Airline::findIdentity($operatedCode) : null;
                $this->parseResult[$i]['operatingAirlineName'] = $operatingAirlineObj->name ?? $operatedCode;

                if ($airline) {
                    $this->parseResult[$i]['cabin'] = $airline->getCabinByClass($parseData['booking_class']);
                }

                $itinerary[] = $this->itinerary[] = (new ItineraryDumpDTO([]))
                    ->feelByParsedReservationDump($this->parseResult[$i]);

                $i++;
            } catch (\Throwable $throwable) {
                $logData = AppHelper::throwableLog($throwable);
                $logData['row'] = $row;
                $logData['parseData'] = $parseData ?? null;
                \Yii::warning($logData, 'ReservationService:parseReservation:Throwable');

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
     * @param array $pastParsedSegments
     * @param int $index
     * @param FlightQuoteSegment $segment
     * @return array
     */
    public function parseSegment(array $pastParsedSegments, int $index, FlightQuoteSegment $segment): array
    {
        $departureTimeZone = null;
        if ($departureAirport = Airports::findByIata($segment->fqs_departure_airport_iata)) {
            $departureTimeZone = new \DateTimeZone($departureAirport->timezone);
        }

        $departureDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $segment->fqs_departure_dt, $departureTimeZone);
        $arrivalDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $segment->fqs_arrival_dt, $departureTimeZone);
        $departureCity = Airports::findByIata($segment->fqs_departure_airport_iata);
        $arrivalCity = Airports::findByIata($segment->fqs_arrival_airport_iata);
        $airline = Airline::find()->where(['iata' => $segment->fqs_operating_airline])->limit(1)->one() ?? null;

        $layoverDuration = $this->getLayoverDurationByPastSegment($pastParsedSegments, $index);
        if ($layoverDuration < 0) {
            \Yii::warning('Negative layover duration (' . $layoverDuration . ' sec) for fqs_id: ' . $segment->fqs_id, 'ReservationService:parseSegment:layoverDuration');
            $layoverDuration = 0;
        }

        $flightDuration = $this->getFlightDurationWithLayover(
            $departureDateTime,
            $arrivalDateTime,
            $layoverDuration
        );
        if ($flightDuration <= 0) {
            \Yii::warning('Negative or zero flight duration (' . $flightDuration . ' sec) for fqs_id: ' . $segment->fqs_id, 'ReservationService:parseSegment:flightDuration');
            $flightDuration = 0;
        }

        return [
            'carrier' => $segment->fqs_operating_airline,
            'airlineObj' => $airline,
            'airlineName' => $airline->name ?? $segment->fqs_operating_airline,
            'departureAirport' => $segment->fqs_departure_airport_iata,
            'arrivalAirport' => $segment->fqs_arrival_airport_iata,
            'segmentIata' => $segment->fqs_departure_airport_iata . $segment->fqs_arrival_airport_iata,
            'departureDateTime' => $departureDateTime,
            'arrivalDateTime' => $arrivalDateTime,
            'flightNumber' => $segment->fqs_flight_number,
            'bookingClass' => $segment->fqs_booking_class,
            'departureCity' => $departureCity,
            'arrivalCity' => $arrivalCity,
            'flightDuration' => $segment->fqs_duration ?? $flightDuration,
            'layoverDuration' => $layoverDuration,
            'operatingAirline' => null,
            'operatingAirlineObj' => null,
            'operatingAirlineName' => null,
            'cabin' => $airline ? $airline->getCabinByClass($segment->fqs_booking_class) : null,
        ];
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
     * @param Airports|null $departureCity
     * @param Airports|null $arrivalCity
     * @return float|int
     */
    private function getFlightDuration(DateTime $departureDateTime, DateTime $arrivalDateTime, ?Airports $departureCity, ?Airports $arrivalCity)
    {
        return intval(($arrivalDateTime->getTimestamp() - $departureDateTime->getTimestamp()) / 60);
    }

    /**
     * @param DateTime $departureDateTime
     * @param DateTime $arrivalDateTime
     * @param int $layover
     * @return float|int
     */
    private function getFlightDurationWithLayover(DateTime $departureDateTime, DateTime $arrivalDateTime, int $layover)
    {
        return intval(($arrivalDateTime->getTimestamp() - $departureDateTime->getTimestamp()) / 60 - $layover);
    }

    /**
     * @param array $data
     * @param int $index
     * @return float|int
     */
    private function getLayoverDuration(array $data, int $index)
    {
        $dataItem = $data[$index] ?? [];
        $dataPrevItem = $data[$index - 1] ?? [];
        if ($dataPrevItem && !FlightQuoteHelper::isNextTrip($dataPrevItem, $dataItem)) {
            $departTs = empty($dataItem['departureDateTime']) ? 0 : $dataItem['departureDateTime']->getTimestamp();
            $arrivTs = empty($dataPrevItem['arrivalDateTime']) ? 0 : $dataPrevItem['arrivalDateTime']->getTimestamp();
            $result = (int) (($departTs - $arrivTs) / 60);
        }
        return $result ?? 0;
    }


    /**
     * @param array $data
     * @param int $index
     * @return float|int
     */
    private function getLayoverDurationByPastSegment(array $data, int $index)
    {
        $data = $this->getArrivalAndDeparture($data);
        $dataItem = $data[$index] ?? [];
        $dataPrevItem = $data[$index - 1] ?? [];
        if ($dataPrevItem && !FlightQuoteHelper::isNextTrip($dataPrevItem, $dataItem)) {
            $departTs = empty($dataItem['departureDateTime']) ? 0 : $dataItem['departureDateTime']->getTimestamp();
            $arrivTs = empty($dataPrevItem['arrivalDateTime']) ? 0 : $dataPrevItem['arrivalDateTime']->getTimestamp();
            $result = (int) (($departTs - $arrivTs) / 60);
        }
        return $result ?? 0;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getArrivalAndDeparture(array $data): array
    {
        $dates = [];
        foreach ($data as $segment) {
            $departureTimeZone = null;
            if ($departureAirport = Airports::findByIata($segment->fqs_departure_airport_iata)) {
                $departureTimeZone = new \DateTimeZone($departureAirport->timezone);
            }

            $departureDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $segment->fqs_departure_dt, $departureTimeZone);
            $arrivalDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $segment->fqs_arrival_dt, $departureTimeZone);
            $dates[] = [
                'departureDateTime' => $departureDateTime,
                'arrivalDateTime' => $arrivalDateTime,
            ];
        }

        return $dates;
    }


    /**
     * @param string $code
     * @param bool $onView
     * @return string
     */
    private function getAirlineName(string $code, bool $onView): string
    {
        $airline = (!$onView) ? Airline::findIdentity($code) : null;
        return $airline->name ?? $code;
    }
}
