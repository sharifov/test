<?php

namespace modules\hotel\src\useCases\api\searchQuote;

use modules\hotel\models\Hotel;

class HotelQuoteSearchGuard
{
	/**
	 * @param Hotel $hotel
	 * @return Hotel
	 */
	public static function guard(Hotel $hotel): Hotel
	{
		if (!$hotel->ph_check_in_date) {
			throw new \DomainException('Check in date is not provided; Fill Hotel data;');
		}

		if (!$hotel->ph_check_out_date) {
			throw  new \DomainException('Check out date is not provided; Fill Hotel data;');
		}

		if (!$hotel->ph_destination_code) {
			throw new \DomainException('Destination is not provided; Fill Hotel data;');
		}

		return $hotel;
	}
}