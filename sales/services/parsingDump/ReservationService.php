<?php

namespace sales\services\parsingDump;

use common\components\SearchService;
use common\models\Airline;
use common\models\Airport;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use sales\services\parsingDump\worldSpan\Reservation;
use yii\base\ErrorException;

/**
 * Class ReservationService
 */
class ReservationService extends Reservation
{
    private CONST ARRIVAL_OFFSET_MAP = [
        '' => 'arrival on the same day',
        '-1' => 'arrival a day earlier',
        '#1' => 'arrival the next day',
        '#2' => 'arrival in a day',
        '#3' => 'arrival in two days',
    ]; /* TODO::  */

    /**
     * @param $string
     * @param bool $validation
     * @param array $itinerary
     * @param bool $onView
     * @return array
     */
    public function parseReservation($string, $validation = true, &$itinerary = [], $onView = false): array
    {
        $result = [];

        $rows = explode("\n", $string);
        foreach ($rows as $key => $row) {

            if (empty($rawData = $this->parseRow($row))) {
                $result['failed'][] = $row;
                continue;
            }
            $parseData = $this->dataMapping($rawData);

            $result['todo'] = $parseData;

            $result['carrier'] = $parseData['airline'];
            $result['airlineName'] = $this->getAirlineName($parseData['airline']);
            $result['departureAirport'] = $parseData['airport_code_from'];
            $result['arrivalAirport'] = $parseData['airport_code_to'];
            $result['departureDateTime'] = $this->createDateTime(
                $parseData['departure_date_day'],
                $parseData['departure_date_month'],
                $parseData['departure_time_hh'],
                $parseData['departure_time_mm']
            );
            $result['arrivalDateTime'] = $this->createDateTime(
                $parseData['departure_date_day'],
                $parseData['departure_date_month'],
                $parseData['departure_time_hh'],
                $parseData['departure_time_mm']
            );

        }

        \yii\helpers\VarDumper::dump($result, 10, true); exit();
        /* FOR DEBUG:: must by remove */

        return $result;
    }

    private function createDateTime(string $day, string $month, string $hour, string $minute)
    {
        $dateFormat = 'dM H:i';
        $dateString = $day . strtolower($month) . ' ' . $hour . ':' . $minute;

		return \DateTime::createFromFormat($dateFormat, $dateString);
    }

    /**
     * @param string $code
     * @return string
     */
    private function getAirlineName(string $code): string
    {
        $airline = Airline::findIdentity($code);
        return $airline ? $airline->name : $code;
    }

    /**
     * @param $string
     * @param bool $validation
     * @param array $itinerary
     * @param bool $onView
     * @return array
     */
    public function parseDumpOld($string, $validation = true, &$itinerary = [], $onView = false): array
    {
        if (!empty($itinerary) && $validation) {
            $itinerary = [];
        }

        $depCity = $arrCity = null;
        $data = [];
        $segmentCount = 0;
        $operatedCnt = 0;
        try {
            $rows = explode("\n", $string);
            foreach ($rows as $row) {
                $row = trim(preg_replace('!\s+!', ' ', $row));
                $rowArr = explode(' ', $row);
                if (!is_numeric($rowArr[0])) {
                    $rowArrAst = explode('*', $rowArr[0]);
                    if (count($rowArrAst) > 1) {
                        array_shift($rowArr);
                        for ($i = count($rowArrAst) - 1; $i >= 0; $i--) {
                            array_unshift($rowArr, $rowArrAst[$i]);
                        }
                    }
                }

                if (stripos($rowArr[0], "OPERATED") !== false) {
                    $idx = count($itinerary);
                    if($idx > 0){
                        $idx--;
                    }
                    if (isset($data[$idx]) && isset($itinerary[$idx])) {
                        $operatedCnt++;
                        $position = stripos($row, "OPERATED BY");
                        $operatedBy = trim(substr($row, $position));
                        $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                        $data[$idx]['operatingAirline'] = $operatedBy;
                        $itinerary[$idx]->operationAirlineCode = $operatedBy;
                    }
                }

                if (!is_numeric((int)$rowArr[0])) continue;

                $segmentCount++;
                $carrier = substr($rowArr[1], 0, 2);
                $depAirport = '';
                $arrAirport = '';
                $depDate = '';
                $arrDate = '';
                $depDateTime = '';
                $arrDateTime = '';
                $flightNumber = '';
                $arrDateInRow = false;
                $operationAirlineCode = '';

                if (stripos($row, "OPERATED BY") !== false) {
                    $position = stripos($row, "OPERATED BY");
                    $operatedBy = trim(substr($row, $position));
                    $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                    $operationAirlineCode = $operatedBy;
                }

                $posCarr = stripos($row, $carrier);
                $rowFl = substr($row, $posCarr+strlen($carrier));
                preg_match('/([0-9]+)\D/', $rowFl, $matches);
                if (!empty($matches)) {
                    $flightNumber = $matches[1];
                }

                preg_match('/\W([A-Z]{6})\W/', $row, $matches);
                if (!empty($matches)) {
                    $depAirport = substr($matches[1], 0, 3);
                    $arrAirport = substr($matches[1], 3, 3);
                }

                preg_match_all("/[0-9]{2}(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)/", $row, $matches);
                if (!empty($matches)) {
                    if (empty($matches[0])) continue;
                    $depDate = $matches[0][0];
                    if (isset($matches[0][1])) {
                        $arrDateInRow = true;
                    }
                    $arrDate = (isset($matches[0][1])) ? $matches[0][1] : $depDate;
                }

                $rowExpl = explode($depAirport . $arrAirport, $row);
                $rowTime = $rowExpl[1];
                preg_match_all('/([0-9]{3,4})(N|A|P)?(\+([0-9])?)?/', $rowTime, $matches);

				if (!empty($matches)) {
					$now = new \DateTime();
					$matches[1][0] = substr_replace($matches[1][0], ':', -2, 0);
					$matches[1][1] = substr_replace($matches[1][1], ':', -2, 0);
					$date = $depDate . ' ' . $matches[1][0];
					if ($matches[2][0] != '') {
						$date = $date . strtolower(str_replace('N', 'P', $matches[2][0])) . 'm';
						$dateFormat = 'jM g:ia';
					}
					else {
						$dateFormat = 'jM H:i';
					}
					$depDateTime = \DateTime::createFromFormat($dateFormat, $date);
					if ($depDateTime == false) {
						continue;
					}
					if ($now->getTimestamp() > $depDateTime->getTimestamp()) {
						$date = date('Y') + 1 . $date;
						$dateFormat = 'Y' . $dateFormat;
						$depDateTime = \DateTime::createFromFormat($dateFormat, $date);
					}
					$date = $arrDate . ' ' . $matches[1][1];
					if ($matches[2][1] != '') {
						$date = $date . strtolower(str_replace('N', 'P', $matches[2][1])) . 'm';
						$dateFormat = 'jM g:ia';
					}
					else {
						$dateFormat = 'jM H:i';
					}
					$arrDateTime = \DateTime::createFromFormat($dateFormat, $date);
					if ($now->getTimestamp() > $arrDateTime->getTimestamp()) {
						$date = date('Y') + 1 . $date;
						$dateFormat = 'Y' . $dateFormat;
						$arrDateTime = \DateTime::createFromFormat($dateFormat, $date);
					}
					$arrDepDiff = $depDateTime->diff($arrDateTime);
					if ($arrDepDiff->d == 0 && !$arrDateInRow && !empty($matches[3][1])) {
						if ($matches[3][1] == "+") {
							$matches[3][1] .= 1;
						}
						$arrDateTime->add(\DateInterval::createFromDateString($matches[3][1] . ' day'));
					}
					$depCity = Airport::findIdentity($depAirport);
					$arrCity = Airport::findIdentity($arrAirport);
				}

                $rowExpl = explode($depDate, $rowFl);
                $cabin = trim(str_replace($flightNumber, '', trim($rowExpl[0])));
                if ($depCity !== null && $arrCity !== null && $depCity->dst != $arrCity->dst) {
                    $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
                    $flightDuration = (int)$flightDuration + ((int)$depCity->dst * 60) - ((int)$arrCity->dst * 60);
                } else {
                    $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
                }

                $airline = null;
                if (!$onView) {
                    $airline = Airline::findIdentity($carrier);
                }

                $segment = [
                    'carrier' => $carrier,
                    'airlineName' => ($airline !== null)
                        ? $airline->name
                        : $carrier,
                    'departureAirport' => $depAirport,
                    'arrivalAirport' => $arrAirport,
                    'departureDateTime' => $depDateTime,
                    'arrivalDateTime' => $arrDateTime,
                    'flightNumber' => $flightNumber,
                    'bookingClass' => $cabin,
                    'departureCity' => $depCity,
                    'arrivalCity' => $arrCity,
                    'flightDuration' => $flightDuration,
                    'layoverDuration' => 0,
                    'arrivalOffset' => $this->getArrivalOffset($row),
                ];
                if($airline !== null){
                    $segment['cabin'] = $airline->getCabinByClass($cabin);
                }
                $segment['cabin'] = SearchService::getCabinRealCode($segment['cabin']);
                if (!empty($operationAirlineCode)) {
                    $segment['operatingAirline'] = $operationAirlineCode;
                }
                if (count($data) != 0 && isset($data[count($data) - 1])) {
                    $previewSegment = $data[count($data) - 1];
                    $segment['layoverDuration'] = ($segment['departureDateTime']->getTimestamp() - $previewSegment['arrivalDateTime']->getTimestamp()) / 60;
                }
                $data[] = $segment;
                $itinerary[] = (new ItineraryDumpDTO([]))->feelByParsedReservationDump($segment);
            }
            if ($validation) {
                if ($segmentCount !== count($data) + $operatedCnt) {
                    $data = [];
                }
            }
        } catch (ErrorException $ex) {
            $data = [];
        }

        return $data;
    }

    /**
     * @param string $row
     * @return string
     */
    private function getArrivalOffset(string $row): string
    {
        $explodeRaw = explode('$', $row);
        $explodeRaw = explode(' ', trim($explodeRaw[0]));
        $offsetRaw = end($explodeRaw);
        $offsetRaw = explode('/', $offsetRaw);
        $offset = $offsetRaw[0];

        if (array_key_exists($offset, self::ARRIVAL_OFFSET_MAP)) {
            return self::ARRIVAL_OFFSET_MAP[$offset];
        }
        return '';
    }

}