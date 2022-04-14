<?php

namespace src\services\parsingDump\lib\sabre;

use common\models\Airports;
use DateTime;
use DateTimeZone;
use src\helpers\app\AppHelper;
use src\services\parsingDump\lib\ParseDumpInterface;
use src\services\parsingDump\lib\ParseReservationInterface;

/**
 * Class Reservation
 */
class Reservation implements ParseDumpInterface, ParseReservationInterface
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
                $logData = AppHelper::throwableLog($throwable);
                $logData['row'] = $row;
                $logData['rawData'] = $rawData ?? null;
                \Yii::warning($logData, 'Sabre:Reservation:parseDump:Throwable');
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
        if ($result =  count($matches) > 12 ? $matches : null) {
            $result['operated'] = self::getOperated($row);
            return $result;
        }
        return [];
    }

    private static function getOperated(string $row): ?string
    {
        $position = stripos($row, "OPERATED BY");
        $operatedBySource = trim(substr($row, $position));
        preg_match('/^(OPERATED\s+BY\s+)([A-Z]{2})/x', $operatedBySource, $matches);

        return $matches[2] ?? null;
    }

    /**
     * @param string|null $pattern
     * @return string
     */
    public static function getPatternRow(?string $pattern = null): string
    {
        return $pattern ?? '/^
            (\d{1,2}) # index
            \s+([A-Z]{2}|[A-Z]{1}\d{1}|\d{1}[A-Z]{1}) # Airline
            \s*(\d{1,4})([A-Z]{1}) # Flight number + Booking Class 
            \s+(\d{2})(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) # Departure day + month
            (\s{1}[A-Z]{1}\s{1}|\s{1})* # ignore 
            \s*([A-Z]{3})([A-Z]{3}) # iata airport departure + arrival           
            (\W[A-Z]{2}\d{0,1}|\s{1})* # ignore
            (\s+\d{1}|\s+\d{2})(\d{2})(N||A||P)\+? # Departure time hours + min + (AM|PM)
            (\s+\d{1}|\s+\d{2})(\d{2})(N||A||P)\+? # Arrival time hours + min + (AM|PM)
            \s+((\d{2})(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC))* # Arrival day + month
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
        $result['departure_airport_iata'] = $data[8];
        $result['arrival_airport_iata'] = $data[9];
        $result['departure_time_hh'] = trim($data[11]);
        $result['departure_time_mm'] = $data[12];
        $result['departure_am_pm'] = $data[13];
        $result['arrival_time_hh'] = trim($data[14]);
        $result['arrival_time_mm'] = $data[15];
        $result['arrival_am_pm'] = $data[16];

        if (isset($data[18], $data[19])) {
            $result['arrival_date_day'] = $data[18];
            $result['arrival_date_month'] = $data[19];
        }
        $result['operated'] = $data['operated'];
        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    private function postProcessing(array $data): array
    {
        $departureTimeZone = null;
        if ($departureAirport = Airports::findByIata($data['departure_airport_iata'])) {
            $departureTimeZone = new \DateTimeZone($departureAirport->timezone);
        }

        $data['departure_date_time'] = $this->createDateTime(
            $data['departure_date_day'],
            $data['departure_date_month'],
            $data['departure_time_hh'],
            $data['departure_time_mm'],
            $data['departure_am_pm'],
            $departureTimeZone
        );
        if ($data['departure_date_time'] === false) {
            throw new \RuntimeException('Parsing and generating Departure DT ended in failure');
        }

        if (isset($data['arrival_date_day'], $data['arrival_date_month'])) {
            $arrivalDateDay = $data['arrival_date_day'];
            $arrivalDateMonth = $data['arrival_date_month'];
        } else {
            $arrivalDateDay = $data['departure_date_day'];
            $arrivalDateMonth = $data['departure_date_month'];
        }

        $arrivalTimeZone = null;
        if ($arrivalAirport = Airports::findByIata($data['arrival_airport_iata'])) {
            $arrivalTimeZone = new \DateTimeZone($arrivalAirport->timezone);
        }

        $data['arrival_date_time'] = $this->createDateTime(
            $arrivalDateDay,
            $arrivalDateMonth,
            $data['arrival_time_hh'],
            $data['arrival_time_mm'],
            $data['arrival_am_pm'],
            $arrivalTimeZone
        );
        if ($data['arrival_date_time'] === false) {
            throw new \RuntimeException('Parsing and generating Arrival DT ended in failure');
        }

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
     * @param DateTimeZone|null $timezone
     * @return DateTime|false
     */
    private function createDateTime(string $day, string $month, string $hour, string $minute, string $ampm, DateTimeZone $timezone = null)
    {
        $dateFormat = 'dM g:i A';
        $ampm = ($ampm === 'A' || $ampm === 'A+') ? 'AM' : 'PM';
        $dateString = $day . strtolower($month) . ' ' . $hour . ':' . $minute . ' ' . $ampm;
        return DateTime::createFromFormat($dateFormat, $dateString, $timezone);
    }

    /* Example row data map
     * 1 DL 967E 16MAY J PHXSLC*HK1 1106A 124P /DCDL*GGVSUD /E
        1 - index
        DL - Airline
        967 - Flight number
        E - Booking Class
        16MAY - Departure day + month
        J - ?
        PHX - iata airport departure
        SLC - iata airport arrival
        *HK1 - HK это статус сегмента, обозначающий что он подтвержден (Confirmed), 1 это количество мест
        1106A - Departure time hours + min + (AM|PM)
        124P - Arrival time hours + min + (AM|PM)
        /DCDL*GGVSUD - DL Это Airline а GGVSUD это Confirmation Number авиалинии.
        /E - элетронный билет
     */
}
