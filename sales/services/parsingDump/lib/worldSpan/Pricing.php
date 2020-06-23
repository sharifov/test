<?php

namespace sales\services\parsingDump\lib\worldSpan;

use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;
use sales\services\parsingDump\PricingService;

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
                $result['validating_carrier'] = $this->parseValidatingCarrier($string);
                $result['prices'] = $prices;
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable), 'WorldSpan:Pricing:parseDump:Throwable');
        }
        return $result;
    }

    /**
     * @param string $string
     * @return string
     */
    private function parseValidatingCarrier(string $string): ?string
    {
        $carrierPattern = "/VALIDATING\s+CARRIER\s+DEFAULT\s+([A-Z]+)/";
        preg_match($carrierPattern, $string, $carrierMatches);

        return $carrierMatches[1] ?? '';
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
            $j = 0;
            $ticketPrices = explode("\n", $ticketPrice);
            array_shift($ticketPrices);

            foreach ($ticketPrices as $key => $value) {
                if ($values = $this->prepareRow($value)) {

                    preg_match('/([A-Z]+)(\d+)/', $values[0], $typeMatches);
                    if (empty($typeMatches)) {
                        continue;
                    }

                    for ($i = 0; $i < (int) $typeMatches[2]; $i++) {
                        $result[$j]['type'] = PricingService::passengerTypeMapping($typeMatches[1]);
                        $result[$j]['fare'] = $values[1] ?? null;
                        $result[$j]['taxes'] = $values[2] ?? '0.00';
                        $j ++;
                    }
                }
            }
        }
        return $result;
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
