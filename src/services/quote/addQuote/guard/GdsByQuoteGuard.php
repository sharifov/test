<?php

namespace src\services\quote\addQuote\guard;

use src\services\parsingDump\lib\ParsingDump;

/**
 * Class GdsByQuoteGuard
 */
class GdsByQuoteGuard
{
    public static function guard(?string $gdsParam): ?string
    {
        if (!$gds = ParsingDump::getGdsByQuote($gdsParam)) {
            throw new \DomainException('This gds(' . $gdsParam . ') cannot be processed');
        }
        return $gds;
    }
}
