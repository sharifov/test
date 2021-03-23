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
            throw new \DomainException('Missing date from in Attraction request; Please update Attraction request;');
        }

        if (!$attraction->atn_date_to) {
            throw  new \DomainException('Missing date to in Attraction request; Please update Attraction request;');
        }

        if (!$attraction->atn_destination) {
            throw new \DomainException('Missing destination in Attraction request; Please update Attraction request;');
        }

        return $attraction;
    }
}
