<?php

namespace modules\attraction\src\useCases\api\searchQuote;

use modules\attraction\models\Attraction;

class AttractionQuoteSearchGuard
{
    /**
     * @param Attraction $attraction
     * @return Attraction
     */
    public static function guard(Attraction $attraction): Attraction
    {
        if (!$attraction->atn_date_from) {
            throw new \DomainException('Missing check in date in Attraction data; Fill Attraction data;');
        }

        if (!$attraction->atn_date_to) {
            throw  new \DomainException('Missing check out date in Attraction data; Fill Attraction data;');
        }

        if (!$attraction->atn_destination) {
            throw new \DomainException('Missing destination in Attraction; Fill Attraction data;');
        }

        return $attraction;
    }
}
