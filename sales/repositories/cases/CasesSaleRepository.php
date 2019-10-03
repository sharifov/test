<?php

namespace sales\repositories\cases;

use common\models\CaseSale;
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
	 * @param string $jsonCaseSaleData
	 * @return array
	 */
	public function decodeSaleData(string $jsonCaseSaleData): array
	{
		return json_decode($jsonCaseSaleData, true);
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

	public function needSyncWithBO(CaseSale $caseSale): void
	{
		$caseSale->css_need_sync_bo = 1;
	}
}