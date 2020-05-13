<?php

namespace sales\services\parsingDump\worldSpan;

/**
 * Class Reservation
 */
class Reservation implements ParseDump
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        $rows = explode("\n", $string);
        foreach ($rows as $key => $row) {

            if (empty($rawData = $this->parseRow($row))) {
                $result['failed'][] = $row;
                continue;
            }

            $parseData = $this->dataMapping($rawData);
            $result['parseData'][$parseData['index']] = $parseData;
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
        $pattern = '/^
            (\d{1,2}) # index
            \s([A-Z]{2}) # Airline
            \s*(\d{2,4})([A-Z]{1}) # Flight number + Booking Class
            \s{1}(\d{1,2})([A-Z]{3}) # Departure Date
            \s{1}([A-Z]{2}) # Departure Day of the week
            \s{1}([A-Z]{3})([A-Z]{3}) # Airport codes from+to
            \s{1}.{3}\s{1,2}(\d{2})(\d{2}) # Departure Time HHMM 
            \s{1,2}(\d{2})(\d{2}) # Arrival Time HHMM  
            (.*?)\/\X|\/\O\ # Arrival offset           
            /x';

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
        $result['airline'] = $data[2];
        $result['flight_number'] = $data[3];
        $result['booking_class'] = $data[4];
        $result['departure_date_day'] = $data[5];
        $result['departure_date_month'] = $data[6];
        $result['departure_day_of_week'] = $data[7];
        $result['departure_airport_iata'] = $data[8];
        $result['arrival_airport_iata'] = $data[9];
        $result['departure_time_hh'] = $data[10];
        $result['departure_time_mm'] = $data[11];
        $result['arrival_time_hh'] = $data[12];
        $result['arrival_time_mm'] = $data[13];
        $result['arrival_offset'] = trim($data[14]);
        $result['cabin'] = $data[15];
        return $result;
    }
}