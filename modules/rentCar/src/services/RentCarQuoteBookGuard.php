<?php

namespace modules\rentCar\src\services;

use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use yii\helpers\ArrayHelper;

/**
 * Class RentCarQuoteBookGuard
 */
class RentCarQuoteBookGuard
{
    public static function guard(RentCarQuote $rentCarQuote): bool
    {
        if ($rentCarQuote->rcqProductQuote->isBooked()) {
            throw new \DomainException('RentCarQuote already booked');
        }
        if (!$rentCarQuote->isBookable()) {
            throw new \DomainException('Product Quote not in allowed status');
        }
        if (!$order = ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pqOrder')) {
            throw new \DomainException('Not found Order');
        }
        return true;
    }
}
