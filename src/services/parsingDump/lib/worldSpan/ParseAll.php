<?php

namespace src\services\parsingDump\lib\worldSpan;

use src\helpers\app\AppHelper;
use src\services\parsingDump\lib\ParseDumpInterface;

/**
 * Class ParseAll
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
            \Yii::error(AppHelper::throwableFormatter($throwable), 'WorldSpan:ParseAll:parseDump:Throwable');
        }
        return $result;
    }
}
