<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelQuote;

/**
 * Class HotelQuoteCheckRateGuard
 * @package modules\hotel\src\useCases\api\bookQuote
 */
class HotelQuoteCheckRateGuard
{
    /**
     * @param HotelQuote $model
     * @return Hotel
     */
    public static function hotel(HotelQuote $model): Hotel
	{
		if (!$hotel = $model->hqHotel) {
            throw new \DomainException('Hotel not found. (ID:' . $model->hq_id . ');');
        }
		return $hotel;
	}

    /**
     * @param HotelQuote $model
     * @return array
     */
    public static function hotelQuoteRooms(HotelQuote $model): array
	{
        if (!$hotelQuoteRooms = $model->hotelQuoteRooms) {
            throw new \DomainException('Hotel Quote Rooms not found. (ID:' . $model->hq_id . ');');
        }
		return $hotelQuoteRooms;
	}
}