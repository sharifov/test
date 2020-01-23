<?php

namespace modules\hotel\src\repositories\hotel;

use modules\hotel\models\Hotel;
use modules\hotel\src\exceptions\HotelCodeException;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

class HotelRepository extends Repository
{
	/**
	 * @param int $id
	 * @return Hotel
	 */
	public function find(int $id): Hotel
	{
		if ($flight = Hotel::findOne($id)) {
			return $flight;
		}
		throw new NotFoundException('Flight is not found', HotelCodeException::HOTEL_NOT_FOUND);
	}
}