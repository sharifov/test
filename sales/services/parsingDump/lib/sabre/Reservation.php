<?php

namespace sales\services\parsingDump\lib\Sabre;

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
                $parseData = $this->dataMapping($rawData);
                $result['reservation'][$parseData['index']] = $parseData;
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableFormatter($throwable), 'Sabre:Reservation:parseDump:Throwable');
            }
        }
        return $result;
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
        preg_match('/([A-Z]{1,2})\z/', $row, $matchesCabin);

        if (count($matches) >= 14) {
            $matches[] = $matchesCabin[1] ?? '';
        } else {
            $matches = [];
        }
        return $matches;
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
        $result['cabin'] = trim($data[16]);
        return $result;
    }

    /**
     * @return string
     */
    public static function getPatternRow(): string
    {
        return '/^
            (\d{1,2}) # index
            (\s{1}|\*)([A-Z]{2}) # Airline
            \s*(\d{2,4})([A-Z]{1}) # Flight number + Booking Class
            \s{1}(\d{1,2})([A-Z]{3}) # Departure Date
            \s{1}([A-Z]{2}) # Departure Day of the week
            \s{1}([A-Z]{3})([A-Z]{3}) # Airport codes from+to
            \s{1}.{3}\s{1,2}(\d{2})(\d{2}) # Departure Time HHMM 
            \s{1,2}(\d{2})(\d{2}) # Arrival Time HHMM  
            (.*?)\/\X|\/\O\ # Arrival offset           
            /x';
    }
}