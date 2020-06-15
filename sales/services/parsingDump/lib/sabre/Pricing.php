<?php

namespace sales\services\parsingDump\lib\sabre;

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
            \Yii::error(AppHelper::throwableFormatter($throwable), 'Sabre:Pricing:parseDump:Throwable');
        }
        return $result;
    }

    /**
     * @param string $string
     * @return string
     */
    private function parseValidatingCarrier(string $string): ?string
    {
        $carrierPattern = "/VALIDATING\s+CARRIER\s+\W\s+([A-Z]+)/";
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
        $ticketPricePattern = "/CHARGES\s+TOTAL\s(.*?)TTL/s";
        preg_match($ticketPricePattern, $string, $ticketPriceMatches);

        if (isset($ticketPriceMatches[1]) && $ticketPriceText = trim($ticketPriceMatches[1])) {
            $j = 0;
            $ticketPrices = explode("\n", $ticketPriceText);
            $pricePattern = '/
                ^(\d{1,2})\- # count pas
                \w|\s+USD(\d+.\d+) # fare
                \s+((\d+.\d+)[A-Z]{1,3})? # taxes
                \s+USD(\d+.\d+)([A-Z]{3}) # amount + type                         
                /x';

            foreach ($ticketPrices as $row) {
                $row = trim($row);

                preg_match('/^(\d{1,2})-/', $row, $matchesCount);
                preg_match($pricePattern, $row, $matches);

                if (isset($matches[1], $matchesCount[1])) {

                    for ($i = 0; $i < (int) $matchesCount[1]; $i++) {
                        $type = $matches[6] ?? null;
                        $result[$j]['type'] = PricingService::passengerTypeMapping($type);
                        $result[$j]['fare'] = $matches[2] ?? null;
                        $result[$j]['taxes'] = !empty($matches[4]) ? $matches[4] : '0.00';
                        $j ++;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param string|null $source
     * @return string
     */
    private function typeMapping(?string $source): string
    {
        switch ($source) {
            case 'ADT': case 'JCB': case 'PFA': case 'ITX': case 'JWZ': case 'WEB':
                $result = 'ADT';
                break;
            case 'CNN': case 'JNN':case 'CBC': case 'INN': case 'PNN': case 'JWC': case 'UNN':
                $result = 'CHD';
                break;
            case 'INF': case 'INS': case 'JNS':case 'CBI': case 'JNF': case 'PNF': case 'ITF': case 'ITS':
                $result = 'INF';
                break;
            default:
                $result = 'ADT';
        }
        return $result;
    }
}
