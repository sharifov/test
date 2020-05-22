<?php

namespace sales\services\parsingDump\lib\Sabre;

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
     * @return string|null
     */
    private function parseValidatingCarrier(string $string): ?string
    {
        $carrierPattern = "/VALIDATING\s+CARRIER\s+\W\s+([A-Z]+)/";
        preg_match($carrierPattern, $string, $carrierMatches);

        return $carrierMatches[1] ?? null;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function parsePrice(string $string): ?array
    {
        $result = null;
        $ticketPricePattern = "/BASE FARE TAXES\/FEES\/CHARGES TOTAL\s(.*?)TTL/s";
        preg_match($ticketPricePattern, $string, $ticketPriceMatches);

        if (isset($ticketPriceMatches[1]) && $ticketPriceText = trim($ticketPriceMatches[1])) {
            $i = 0;
            $ticketPrices = explode("\n", $ticketPriceText);
            $pricePattern = '/
                (\d{1,2})-
                \s+USD(\d+.\d+) # fare
                \s+(\d+.\d+)[A-Z]{1,3} # taxes
                \s+USD(\d+.\d+)([A-Z]{3}) # amount + type                         
                /x';

            foreach ($ticketPrices as $row) {
                preg_match($pricePattern, $row, $matches);
                if (isset($matches[1])) {
                    $type = $matches[5] ?? null;
                    $result[$i]['type'] = $this->typeMapping($type);
                    $result[$i]['fare'] = $matches[2] ?? null;
                    $result[$i]['taxes'] = $matches[3] ?? null;
                    $i ++;
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
            case 'ADT': case 'JCB': case 'PFA': case 'ITX': case 'WEB':
                $result = 'ADT';
                break;
            case 'CNN': case 'JNN': case 'PNN': case 'INN':
                $result = 'CHD';
                break;
            case 'INF': case 'JNF': case 'PNF': case 'ITF':
                $result = 'INF';
                break;
            default:
                $result = 'ADT';
        }
        return $result;
    }
}
