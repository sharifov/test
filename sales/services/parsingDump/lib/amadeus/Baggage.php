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
        return [];
    }
}
