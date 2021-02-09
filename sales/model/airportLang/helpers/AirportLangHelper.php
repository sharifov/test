<?php

namespace sales\model\airportLang\helpers;

/**
 * Class AirportLangHelper
 */
class AirportLangHelper
{
    /**
     * @param string $locale for example en-US
     * @return string
     */
    public static function getLangFromLocale(string $locale): string
    {
        return strtoupper(
            substr(
                explode('-', $locale, 1)[0],
                0,
                2
            )
        );
    }
}
