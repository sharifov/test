<?php

namespace modules\rentCar\src\services;

use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\repositories\rentCar\RentCarQuoteRepository;
use sales\auth\Auth;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class RentCarQuoteCancelBookService
 */
class RentCarQuoteCancelBookService
{
    public static function book(RentCarQuote $rentCarQuote): bool
    {
        self::guard($rentCarQuote);

        /* TODO::  */

        return true;
    }

    public static function guard(RentCarQuote $rentCarQuote)
    {
        if (!$rentCarQuote->rcqProductQuote->isBooked()) {
            throw new \DomainException('RentCarQuote not booked');
        }
        return true;
    }
}
