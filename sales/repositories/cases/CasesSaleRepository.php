<?php

namespace sales\repositories\cases;

use common\models\CaseSale;
use sales\entities\cases\Cases;
use sales\repositories\NotFoundException;

class CasesSaleRepository
{
	/**
	 * @param int $caseId
	 * @param int $caseSaleId
	 * @return CaseSale
	 */
	public function getSaleByPrimaryKeys(int $caseId, int $caseSaleId): CaseSale
	{
		if ($caseSale = CaseSale::find()->where(['css_cs_id' => $caseId, 'css_sale_id' => $caseSaleId])->one()) {
			return $caseSale;
		}
		throw new NotFoundException('Case sale is not found');
	}

	/**
	 * @param CaseSale $caseSale
	 * @param array $oldCaseSaleData
	 * @param array $newCaseSaleData
	 * @return void
	 */
	public function updateSaleData(CaseSale $caseSale, array $oldCaseSaleData, array $newCaseSaleData): void
	{
		$caseSale->css_sale_data_updated = json_encode( array_replace_recursive($oldCaseSaleData, $newCaseSaleData) );
	}

	/**
	 * @param CaseSale $caseSale
	 * @param bool $value
	 */
	public function updateSyncWithBOField(CaseSale $caseSale, bool $value): void
	{
		$caseSale->css_need_sync_bo = $value ? 1 : 0;
	}

	/**
	 * @param CaseSale $caseSale
	 * @param string $newData
	 */
	public function updateOriginalSaleData(CaseSale $caseSale, string $newData = ''): void
	{
		if (!empty($newData)) {
			$caseSale->css_sale_data = $newData;
		} else {
			$caseSale->css_sale_data = $caseSale->css_sale_data_updated;
		}
	}

	/**
	 * @param CaseSale $caseSale
	 * @param Cases $case
	 * @param array $saleData
	 * @return CaseSale
	 */
	public function refreshOriginalSaleData(CaseSale $caseSale, Cases $case, array $saleData): CaseSale
	{
		$caseSale->css_cs_id = $case->cs_id;
		$caseSale->css_sale_id = $saleData['saleId'];
		$caseSale->css_sale_data = json_encode($saleData);
		$caseSale->css_sale_pnr = $saleData['pnr'] ?? null;
		$caseSale->css_sale_created_dt = $saleData['created'] ?? null;
		$caseSale->css_sale_book_id = $saleData['bookingId'] ?? null;
		$caseSale->css_sale_pax = isset($saleData['passengers']) && is_array($saleData['passengers']) ? count($saleData['passengers']) : null;
		$caseSale->css_sale_data_updated = json_encode($saleData);
		$caseSale->css_need_sync_bo = 0;
		$caseSale->css_fare_rules = isset($saleData['fareRules']) ?
		    @json_encode($saleData['fareRules']) : null;

		return $caseSale;
	}

	/**
	 * @param CaseSale $caseSale
	 * @return CaseSale
	 */
	public function save(CaseSale $caseSale): CaseSale
	{
		if (!$caseSale->save()) {
			throw new \RuntimeException('Saving error');
		}
		return $caseSale;
	}
}