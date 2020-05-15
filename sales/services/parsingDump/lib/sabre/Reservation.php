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

        preg_match(self::getPatternRow(), $row, $matches);

        return count($matches) > 12 ? $matches : [];
    }

    public static function getPatternRow(): string
    {
        return '/^
            (\d{1,2}) # index
            \s{1}([A-Z]{2}) # Airline
            \s*(\d{1,4})([A-Z]{1}) # Flight number + Booking Class
            \s+(\d{2})(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) # Departure day + month 
            \s+([A-Z]{1}) # ? день недели
            \s+([A-Z]{3})([A-Z]{3}) # iata airport departure + arrival
            \*([A-Z]{2}\d{1}) # ?  
            (\s{1}\d{1}|\s{1}\d{2})(\d{2})(N|A|P) # Departure time hours + min + (AM|PM) 
            (\s{1}\d{1}|\s{1}\d{2})(\d{2})(N|A|P) # Arrival time hours + min + (AM|PM)  
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

    /*
     * 1 DL 967E 16MAY J PHXSLC*HK1 1106A 124P /DCDL*GGVSUD /E
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