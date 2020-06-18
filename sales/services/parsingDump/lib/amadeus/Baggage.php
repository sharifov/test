<?php

namespace sales\services\parsingDump\lib\amadeus;

use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;

/**
 * Class Baggage
 */
class Baggage implements ParseDumpInterface
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        try {
            if ($baggage = self::parseRows($string)) {
                $result['baggage'] = $baggage;
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable), 'Amadeus:Baggage:parseDump:Throwable');
        }
        return $result;
    }

    /**
     * @param string $dump
     * @return array|null
     */
    private static function parseRows(string $dump): ?array
    {
        $result = [];
        $ticketRows = explode("\n", $dump);

        foreach ($ticketRows as $numRow => $row) {
            $row = trim($row);
            preg_match('/(\d)P\z/', $row, $baggageMatches);

            if (isset($baggageMatches[1], $ticketRows[$numRow - 1])) {
                $previewRow = trim($ticketRows[$numRow - 1]);
                $iataPattern = '/^X([A-Z]{3})\s[A-Z]|([A-Z]{3})\s[A-Z]|([A-Z]{3})\z/';

                preg_match($iataPattern, $previewRow, $iataDepartureMatches);
                preg_match($iataPattern, $row, $iataArrivalMatches);

                $iataDeparture = self::getIata($iataDepartureMatches);
                $iataArrival = self::getIata($iataArrivalMatches);

                if ($iataDeparture && $iataArrival) {
                    $result[] = [
                        'segment' => $iataDeparture . $iataArrival,
                        'free_baggage' => [
                            'piece' => $baggageMatches[1],
                            'weight' => '',
                            'height' => '',
                            'price' => 'USD0'
                        ],
                    ];
                }
            }
        }
        return count($result) ? $result : null;
    }

    /**
     * @param array $matches
     * @return string|null
     */
    private static function getIata(array $matches): ?string
    {
        if (empty($matches) || !isset($matches[1])) {
            return null;
        }
        $iata = end($matches);
        return (strlen($iata) === 3) ? $iata : null;
    }
}
