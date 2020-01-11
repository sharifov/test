<?php


namespace modules\flight\src\helpers;


use modules\flight\models\FlightSegment;
use yii\helpers\ArrayHelper;

class FlightSegmentHelper
{
	/**
	 * @return array
	 */
	public static function flexibilityList(): array
	{
		return [
			0 => 0,
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4
		];
	}

	/**
	 * @return array
	 */
	public static function flexibilityTypeList(): array
	{
		return FlightSegment::FLEX_TYPE_LIST;
	}

	/**
	 * @param string $type
	 * @return string
	 */
	public static function flexibilityTypeName(string $type): string
	{
		return ArrayHelper::getValue(self::flexibilityTypeList(), $type);
	}
}