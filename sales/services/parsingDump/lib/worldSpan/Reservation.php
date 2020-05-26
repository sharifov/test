<?php

namespace sales\services\parsingDump\lib\worldSpan;

use DateTime;
use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;

/**
 * Class Reservation
 */
class Reservation implements ParseDumpInterface
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        $string = trim($string);
        $rows = explode("\n", $string);
        foreach ($rows as $key => $row) {
            try {
                if (empty($rawData = $this->parseRow($row))) {
                    continue;
                }
                $parseData = $this->processingRow($rawData);

                $result['reservation'][$parseData['index']] = $parseData;
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableFormatter($throwable), 'WorldSpan:Reservation:parseDump:Throwable');
            }
        }
        return $result;
    }

    /**
     * @param array $rawData
     * @return array
     */
    public function processingRow(array $rawData): array
    {
        $parseData = $this->dataMapping($rawData);
        $parseData = $this->postProcessing($parseData);
        $parseData = $this->removeTrash($parseData, self::getTemporaryKeys());
        return $parseData;
    }

    /**
     * @param string $row
     * @return array
     */
    public function parseRow(string $row): array
    {
        $row = trim($row);
        $pattern = self::getPatternRow();
        preg_match($pattern, $row, $matches);
        return count($matches) >= 14 ? $matches : [];
    }


    /**
     * @param string|null $pattern
     * @return string
     */
    public static function getPatternRow(?string $pattern = null): string
    {
        return $pattern ?? '/^
            (\d{1,2}) # index
            (\s{1}|\*)([A-Z]{2}|[A-Z]{1}\d{1}) # Airline
            \s*(\d{2,4})([A-Z]{1}) # Flight number + Booking Class
            \s{1}(\d{1,2})([A-Z]{3}) # Departure Date
            \s{1}([A-Z]{2}) # Departure Day of the week
            \s{1}([A-Z]{3})([A-Z]{3}) # Airport codes from+to
            \s{1}.{3}\s{1,2}(\d{2})(\d{2}) # Departure Time HHMM 
            \s{1,2}(\d{2})(\d{2}) # Arrival Time HHMM  
            (.*?)\/\X|\/\O\ # Arrival offset           
            /x';
    }

    /**
     * @param array $data
     * @return array
     */
    public function dataMapping(array $data): array
    {
        $result['index'] = $data[1];
        $result['airline'] = $data[3];
        $result['flight_number'] = $data[4];
        $result['booking_class'] = $data[5];
        $result['departure_date_day'] = $data[6];
        $result['departure_date_month'] = $data[7];
        $result['departure_day_of_week'] = $data[8];
        $result['departure_airport_iata'] = $data[9];
        $result['arrival_airport_iata'] = $data[10];
        $result['departure_time_hh'] = $data[11];
        $result['departure_time_mm'] = $data[12];
        $result['arrival_time_hh'] = $data[13];
        $result['arrival_time_mm'] = $data[14];
        $result['arrival_offset'] = trim($data[15]);
        return $result;
    }

    /**
     * @param string $day
     * @param string $month
     * @param string $hour
     * @param string $minute
     * @return DateTime|false
     */
    public function createDateTime(string $day, string $month, string $hour, string $minute)
    {
        $dateFormat = 'dM H:i';
        $dateString = $day . strtolower($month) . ' ' . $hour . ':' . $minute;
        return DateTime::createFromFormat($dateFormat, $dateString);
    }

    private function postProcessing(array $data): array
    {
        $data['departure_date_time'] = $this->createDateTime(
            $data['departure_date_day'],
            $data['departure_date_month'],
            $data['departure_time_hh'],
            $data['departure_time_mm']
        );
        $data['arrival_date_time'] = $this->getArrivalDateTime(
            $data['departure_date_time'],
            $data['arrival_time_hh'],
            $data['arrival_time_mm'],
            $data['arrival_offset']
        );
        return $data;
    }

    /**
     * @param DateTime $departureDateTime
     * @param string $arrivalHour
     * @param string $arrivalMinute
     * @param string $arrivalOffset
     * @throws \Exception
     */
    public function getArrivalDateTime(?DateTime $departureDateTime,  string $arrivalHour, string $arrivalMinute, string $arrivalOffset)
    {
        if (!$departureDateTime) {
            return null;
        }
        $sourceDate = clone $departureDateTime;
        $arrivalOffset = $this->prepareArrivalOffset($arrivalOffset);
        if ($arrivalOffset === 0) {
            $result = $sourceDate->setTime($arrivalHour, $arrivalMinute);
        } else {
            $result = $sourceDate->modify($arrivalOffset . ' day')->setTime($arrivalHour, $arrivalMinute);
        }
		return $result;
    }

    /**
     * @param string $arrivalOffset
     * @return string
     */
    public function prepareArrivalOffset(string $arrivalOffset): string
    {
		$arrivalOffset = ($arrivalOffset === '') ? '0' : $arrivalOffset;
		return str_replace('#', '+', $arrivalOffset);
    }

    /**
     * @param array|null $temporaryKeys
     * @return array
     */
    private static function getTemporaryKeys(?array $temporaryKeys = null): array
    {
        return $temporaryKeys ?? [
            'departure_date_day', 'departure_date_month', 'departure_time_hh', 'departure_time_mm',
            'arrival_date_day', 'arrival_date_month', 'arrival_time_hh', 'arrival_time_mm',
            'departure_day_of_week', 'arrival_offset',
        ];
    }

    /**
     * @param array $data
     * @param array $temporaryKeys
     * @return array
     */
    private function removeTrash(array $data, array $temporaryKeys): array
    {
        foreach ($temporaryKeys as $value) {
            unset($data[$value]);
        }
        return $data;
    }
}