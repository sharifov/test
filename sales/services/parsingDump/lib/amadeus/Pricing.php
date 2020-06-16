<?php

namespace sales\services\parsingDump\lib\amadeus;

use common\models\QuotePrice;
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
            \Yii::error(AppHelper::throwableFormatter($throwable), 'amadeus:Pricing:parseDump:Throwable');
        }
        return $result;
    }

    /**
     * @param string $string
     * @return string
     */
    private function parseValidatingCarrier(string $string): ?string
    {
        $carrierPattern = "/VALIDATING\s+CARRIER\s+([A-Z]+)/";
        preg_match($carrierPattern, $string, $carrierMatches);

        return $carrierMatches[1] ?? '';
    }

    /**
     * @param string $string
     * @return array|null
     */
    private function parsePrice(string $string): ?array
    {
        if ($priceMultipleType = $this->parsePriceMultipleType($string)) {
            return $priceMultipleType;
        }
        if ($priceSingleType = $this->parsePriceSingleType($string)) {
            return $priceSingleType;
        }
        return null;
    }

    /**
     * @param string $dump
     * @return array|null
     */
    private function parsePriceSingleType(string $dump): ?array
    {
        $j = 0;
        $result = null;

        if ($countPassengers = self::getCountPassengers($dump)) {
            $ticketRows = explode("\n", $dump);
            $passengerType = self::getPassengerType($ticketRows);

            if (isset($countPassengers, $passengerType)) {
                $fare = self::getFare($dump);
                $taxes = self::getTaxes($ticketRows);

                for ($i = 0; $i < $countPassengers; $i++) {
                    $result[$j]['type'] = $passengerType;
                    $result[$j]['fare'] = $fare;
                    $result[$j]['taxes'] = $taxes;
                    $j ++;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $dump
     * @return int|null
     */
    private static function getCountPassengers(string $dump): ?int
    {
        $countPattern = '/
            ^01\s{1}P1\W*(\d*) # count passengers                                
            /x';
        preg_match($countPattern, $dump, $countMatches);
        if (isset($countMatches[1])) {
            return (is_numeric($countMatches[1])) ? $countMatches[1] : 1;
        }
        return null;
    }

    /**
     * @param array $ticketRows
     * @return float
     */
    private static function getTaxes(array $ticketRows): float
    {
        $result = 0.00;
        foreach ($ticketRows as $key => $row) {
            $row = trim($row);
            $taxesPattern = '/
                ^USD\s+(\d+.\d{2})\-{1}[A-Z]{2} # taxes                              
                /x';
            preg_match_all($taxesPattern, $row, $taxesMatches);

            if (count($taxesMatches[1])) {
                $result += $taxesMatches[1][0];
            }
        }
        return $result;
    }


    /**
     * @param string $string
     * @return string|null
     */
    private static function getFare(string $string): ?string
    {
        $farePattern = '/
            USD\s+(\d+.\d{2})\s+ # fare                              
            /x';
        preg_match($farePattern, $string, $fareMatches);
        return $fareMatches[1] ?? null;
    }

    /**
     * @param array $ticketRows
     * @return string|null
     */
    private static function getPassengerType(array $ticketRows): ?string
    {
        $result = null;
        foreach ($ticketRows as $numRow => $row) {
            $row = trim($row);
            if ((strpos($row, 'FARE BASIS') !== false) && isset($ticketRows[$numRow + 2])) {
                $passengerTypeRow = $ticketRows[$numRow + 2];
                if (strpos($passengerTypeRow, '/IN') !== false) {
                    $result = QuotePrice::PASSENGER_INFANT;
                } elseif (strpos($passengerTypeRow, '/CH') !== false) {
                    $result = QuotePrice::PASSENGER_CHILD;
                } else {
                    $result = QuotePrice::PASSENGER_ADULT;
                }
                break;
            }
        }
        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     */
    private function parsePriceMultipleType(string $string): ?array
    {
        $result = null;
        $j = 0;
        $pricePattern = '/
            ^\d{2}
            \s+(\d+|\d+\-\d+) # ignore
            \s+([A-Z]{3}) # type 
            \s+(\d{1,2}) # count passengers
            \s+(\d+.\d+) # fare    
            \s+(\d+.\d+) # taxes                    
            /x';
        $ticketPrices = explode("\n", $string);

        foreach ($ticketPrices as $row) {
            $row = trim($row);
            preg_match($pricePattern, $row, $priceMatches);

            if (isset($priceMatches[2], $priceMatches[3])) {
                for ($i = 0; $i < (int) $priceMatches[3]; $i++) {
                    $result[$j]['type'] = PricingService::passengerTypeMapping($priceMatches[2]);
                    $result[$j]['fare'] = $priceMatches[4] ?? null;
                    $result[$j]['taxes'] = !empty($priceMatches[5]) ? $priceMatches[5] : '0.00';
                    $j ++;
                }
            }
        }
        return $result;
    }


}
