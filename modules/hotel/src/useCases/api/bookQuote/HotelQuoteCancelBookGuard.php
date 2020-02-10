<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\models\HotelQuote;

/**
 * Class HotelQuoteCancelBookGuard
 * @package modules\hotel\src\useCases\api\bookQuote
 */
class HotelQuoteCancelBookGuard
{
    /**
     * @param HotelQuote $model
     * @return HotelQuote
     */
    public static function guard(HotelQuote $model): HotelQuote
	{
        if (!$model->isBooking()) {
            throw new \DomainException('Hotel Quote not booked;');
        }
		return $model;
	}
}