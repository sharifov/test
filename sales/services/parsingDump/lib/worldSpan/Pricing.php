<?php

namespace sales\services\parsingDump\lib\worldSpan;

use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;

/**
 * Class Pricing
 */
class Pricing implements ParseDumpInterface
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        try {
            if ($prices = $this->parsePrice($string)) {
                $result['iata'] = $this->parseIata($string);
                $result['price'] = $prices;
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable), 'WorldSpan:Pricing:parseDump:Throwable');
        }
        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function parsePrice(string $string): ?array
    {
        $result = null;
        $ticketPricePattern = '/TICKET (.*?)\*TTL/s';
        preg_match($ticketPricePattern, $string, $ticketPriceMatches);

        if (isset($ticketPriceMatches[1]) && $ticketPrice = trim($ticketPriceMatches[1])) {
            $ticketPrices = explode("\n", $ticketPrice);
            array_shift($ticketPrices);

            foreach ($ticketPrices as $key => $value) {
                if ($values = $this->prepareRow($value)) {
                    $result['tickets'][$key]['name'] = $values[0] ?? null;
                    $result['tickets'][$key]['fare'] = $values[1] ?? null;
                    $result['tickets'][$key]['taxes'] = $values[2] ?? null;
                    $result['tickets'][$key]['amount'] = $values[3] ?? null;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $string
     * @return string|null
     */
    public function parseIata(string $string): ?string
    {
        $airlinePattern = '/CARRIER DEFAULT\s(.*?)\n\*/';
        preg_match($airlinePattern, $string, $airlineMatches);
        return isset($airlineMatches[1]) ? trim($airlineMatches[1]) : null;
    }

    /**
     * @param string $row
     * @return false|string[]
     */
    private function prepareRow(string $row)
    {
        $value = trim($row);
        $value = preg_replace('|[\s]+|s', ' ', $value);
        return explode(' ', $value);
    }
}
