<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\models\HotelQuote;

class HotelQuoteCancelBookGuard
{
    /**
     * @param int $id
     * @return HotelQuote
     */
    public static function guard(int $id): HotelQuote
	{
		if (!$model = HotelQuote::findOne($id)) {
            throw new \DomainException('Hotel Quote not found;');
        }
        if (!$model->isBooking()) {
            throw new \DomainException('Hotel Quote not booked;');
        }
		return $model;
	}
}