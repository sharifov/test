<?php

namespace sales\services\quote\addQuote\guard;

use sales\services\parsingDump\lib\ParsingDump;

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
