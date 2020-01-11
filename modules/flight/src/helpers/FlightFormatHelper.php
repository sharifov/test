<?php

namespace modules\flight\src\helpers;

use modules\flight\models\Flight;
use yii\helpers\ArrayHelper;

class FlightFormatHelper
{
	/**
	 * @return array
	 */
	public static function adultsChildrenInfantsList(): array
	{
		return array_combine(range(0, 9), range(0, 9));
	}

	/**
	 * @param string|null $type
	 * @return string|null
	 */
	public static function cabinName(?string $type): ?string
	{
		return ArrayHelper::getValue(Flight::getCabinClassList(), $type);
	}

	/**
	 * @return array
	 */
	public static function tripTypeList(): array
	{
		return [
			Flight::TRIP_TYPE_ONE_WAY => 'One Way',
			Flight::TRIP_TYPE_ROUND_TRIP => 'Round Trip',
			Flight::TRIP_TYPE_MULTI_DESTINATION => 'Multi destination'
		];
	}
}