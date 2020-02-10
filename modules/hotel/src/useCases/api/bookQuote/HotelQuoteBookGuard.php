<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\models\HotelQuote;

/**
 * Class HotelQuoteBookGuard
 * @package modules\hotel\src\useCases\api\bookQuote
 */
class HotelQuoteBookGuard
{
    /**
     * @param HotelQuote $model
     * @return HotelQuote
     */
    public static function guard(HotelQuote $model): HotelQuote
	{
        if ($model->isBooking()) {
            throw new \DomainException('Hotel Quote already booked. (BookingId:' . $model->hq_booking_id . ')');
        }
        if (!$model->isBookable()) {
            throw new \DomainException('Product Quote not in allowed status');
        }
		return $model;
	}
}