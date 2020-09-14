<?php
namespace sales\helpers\cases;


use common\models\CaseSale;

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

	public static function getTransactionDate(array $saleData): string
	{
		if (!empty($saleData['authList'])) {
			return $saleData['authList'][count($saleData['authList'])-1]['created'] ?? '';
		}
		return '';
	}

	public static function getRecallCommission(array $saleData): float
	{
		if (!empty($saleData['refundRules'])) {
			$cntPassengers = self::getPassengersCountExceptInf($saleData['passengers'] ?? []);
			return (float)($saleData['refundRules']['recall_commission'] ?? 0) / ($cntPassengers ?: 1);
		}
		return 0.00;
	}

	public static function getPassengersCountExceptInf(array $passengers): int
	{
		$cnt = 0;
		foreach ($passengers as $passenger) {
			if ($passenger['type'] !== 'INF') {
				$cnt++;
			}
		}
		return $cnt;
	}

	public static function getCustomerEmail(array $data): string
	{
		return $data['email'] ?? '';
	}
}