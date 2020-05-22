<?php
namespace sales\helpers\cases;


class CaseSaleHelper
{
	/**
	 * @param array $saleData
	 * @return string
	 */
	public static function getCardNumbers(array $saleData): string
	{
		if (!empty($saleData['authList'])) {
			return implode(',', array_map(static function ($arr) {
				return $arr['ccNumber'];
			}, $saleData['authList']));
		}
		return '';
	}
}