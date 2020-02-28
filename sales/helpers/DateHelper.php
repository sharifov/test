<?php
namespace sales\helpers;


class DateHelper
{
	public static function getMonthList(): array
	{
		return [
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July ',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December'];
	}

	/**
	 * @param int $monthNumber
	 * @return mixed|null
	 */
	public static function getMonthName(int $monthNumber)
	{
		return self::getMonthList()[$monthNumber] ?? null;
	}
}