<?php

namespace modules\hotel\src\helpers;

class HotelFormatHelper
{
	/**
	 * @param array $destinations
	 * @param string $term
	 * @return array
	 */
	public static function formatRows(array $destinations, string $term): array
	{
		$result = [];
		if (!isset($destinations['data']['destinations']) || !is_array($destinations['data']['destinations'])) {
			return $destinations;
		}
		foreach ($destinations['data']['destinations'] as $key => $destination) {
			$text = '';
			$selection = '';
			if (!empty($destination['country'])) {
				$text .= self::formatText($destination['country'], $term);
				$selection .= $destination['country'];
			}
			if (!empty($destination['zoneName'])) {
				$text .= ', ' . self::formatText($destination['zoneName'], $term);
				$selection .= ', ' . $destination['zoneName'];
			}
			if (!empty($destination['name'])) {
				$text .= ', ' . self::formatText($destination['name'], $term);
				$selection .= ', ' . $destination['name'];
			}
			if (!empty($destination['hotelName'])) {
				$text .= ', Hotel - ' . self::formatText($destination['hotelName'], $term);
				$selection .= ', ' . $destination['hotelName'];
			}
			$result[$key]['text'] = $text;
			$result[$key]['selection'] = $selection;
			$result[$key]['id'] = $selection;
			$result[$key]['code'] = $destination['code'] ?? null;
			$result[$key]['zone_code'] = $destination['zoneCode'] ?? null;
			$result[$key]['hotel_code'] = $destination['hotelCode'] ?? null;
		}
		return $result;
	}

	/**
	 * @param string $str
	 * @param string $term
	 * @return string
	 */
	public static function formatText(string $str, string $term): string
	{
		return preg_replace('~'.$term.'~i', '<b style="color: #e15554"><u>$0</u></b>', $str);
	}
}