<?php

namespace sales\services\quote\addQuote\guard;

use sales\services\parsingDump\lib\ParsingDump;

/**
 * Class GdsByQuoteGuard
 */
class GdsByQuoteGuard
{
    /**
     * @param string|null $gds
     * @return string
     */
    public static function guard(?string $gds): ?string
    {
        if (!$gds = ParsingDump::getGdsByQuote($gds)) {
            throw new \DomainException(  'This gds(' . $gds . ') cannot be processed');
        }
        return $gds;
    }
}