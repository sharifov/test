<?php

namespace sales\services\parsingDump\lib\amadeus;

use DateTime;
use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;
use sales\services\parsingDump\ReservationService;

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
                \Yii::error(AppHelper::throwableFormatter($throwable), 'amadeus:Reservation:parseDump:Throwable');
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
        preg_match(self::getPatternRow(), $row, $matches);
        return count($matches) > 12 ? $matches : [];
    }

    /**
     * @param string|null $pattern
     * @return string
     */
    public static function getPatternRow(?string $pattern = null): string
    {
        return $pattern ?? '/^
            (\d{1,2}) # index
            \s+([A-Z]{2}|[A-Z]{1}\d{1}) # Airline
            \s*(\d{1,4})\s+([A-Z]{1}) # Flight number + Booking Class 
            \s+(\d{1,2})(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) # Departure day + month 
            \s+\w{1,2} # ignore
            \W([A-Z]{3})([A-Z]{3}) # iata airport departure + arrival 
            \s+\w{1,4} # ignore  
            \s+(\d{1}|\d{2})(\d{2})(A|P) # Departure time hours + min + (AM|PM)
            \s+(\d{1}|\d{2})(\d{2})(A|P) # Arrival time hours + min + (AM|PM)  
            \s+(\d{1,2})(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) # Arrival day + month      
            /x';
    }

    /**
     * @param array $data
     * @return array
     */
    public function dataMapping(array $data): array
    {
        $result['index'] = $data[1];
        $result['airline'] = $data[2];
        $result['flight_number'] = $data[3];
        $result['booking_class'] = $data[4];
        $result['departure_date_day'] = $data[5];
        $result['departure_date_month'] = $data[6];
        $result['departure_airport_iata'] = $data[7];
        $result['arrival_airport_iata'] = $data[8];
        $result['departure_time_hh'] = trim($data[9]);
        $result['departure_time_mm'] = $data[10];
        $result['departure_am_pm'] = $data[11];
        $result['arrival_time_hh'] = trim($data[12]);
        $result['arrival_time_mm'] = $data[13];
        $result['arrival_am_pm'] = $data[14];

        if (isset($data[15], $data[16])) {
            $result['arrival_date_day'] = $data[15];
            $result['arrival_date_month'] = $data[16];
        }
        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    private function postProcessing(array $data): array
    {
        $data['departure_date_time'] = $this->createDateTime(
            $data['departure_date_day'],
            $data['departure_date_month'],
            $data['departure_time_hh'],
            $data['departure_time_mm'],
            $data['departure_am_pm']
        );

        if (isset($data['arrival_date_day'], $data['arrival_date_month'])) {
            $arrivalDateDay = $data['arrival_date_day'];
            $arrivalDateMonth = $data['arrival_date_month'];
        } else {
            $arrivalDateDay = $data['departure_date_day'];
            $arrivalDateMonth = $data['departure_date_month'];
        }

        $data['arrival_date_time'] = $this->createDateTime(
            $arrivalDateDay,
            $arrivalDateMonth,
            $data['arrival_time_hh'],
            $data['arrival_time_mm'],
            $data['arrival_am_pm']
        );

        return $data;
    }

    /**
     * @param array|null $temporaryKeys
     * @return array|string[]
     */
    private static function getTemporaryKeys(?array $temporaryKeys = null): array
    {
        return $temporaryKeys ?? [
            'departure_date_day', 'departure_date_month', 'departure_time_hh', 'departure_time_mm', 'departure_am_pm',
            'arrival_date_day', 'arrival_date_month', 'arrival_time_hh', 'arrival_time_mm', 'arrival_am_pm',
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

    /**
     * @param string $day
     * @param string $month
     * @param string $hour
     * @param string $minute
     * @param string $ampm
     * @return DateTime|false
     */
    private function createDateTime(string $day, string $month, string $hour, string $minute, string $ampm)
    {
        $dateFormat = 'dM g:i A';
        $ampm = ($ampm === 'A') ? 'AM' : 'PM';
        $dateString = $day . strtolower($month) . ' ' . $hour . ':' . $minute . ' ' . $ampm;
        return DateTime::createFromFormat($dateFormat, $dateString);
    }
}