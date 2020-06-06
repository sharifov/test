<?php

namespace sales\services\parsingDump\lib\amadeus;

use sales\helpers\app\AppHelper;
use sales\services\parsingDump\lib\ParseDumpInterface;

/**
 * Class Reservation
 */
class ParseAll implements ParseDumpInterface
{
    /**
     * @param string $string
     * @return array
     */
    public function parseDump(string $string): array
    {
        $result = [];
        try {
            $result[] = (new Reservation())->parseDump($string);
            $result[] = (new Pricing())->parseDump($string);
            $result[] = (new Baggage())->parseDump($string);
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableFormatter($throwable), 'amadeus:ParseAll:parseDump:Throwable');
        }
        return $result;
    }
}