<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\models\HotelQuote;

class HotelQuoteBookGuard
{
    /**
     * @param int $id
     * @return HotelQuote
     */
    public static function guard(int $id): HotelQuote
	{
		if (!$model = HotelQuote::findOne($id)) {
            throw new \DomainException('Hotel Quote not found');
        }
        if ($model->isBooking()) {
            throw new \DomainException('Hotel Quote already booked. (BookingId:' . $model->hq_booking_id . ')');
        }
        if (!$model->isBookable()) {
            throw new \DomainException('Product Quote not in allowed status');
        }
		return $model;
	}
}