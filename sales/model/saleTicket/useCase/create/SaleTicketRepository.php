<?php

namespace sales\model\saleTicket\useCase\create;

use sales\model\saleTicket\entity\SaleTicket;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;
use yii\db\ActiveRecord;

class SaleTicketRepository extends Repository
{
	/**
	 * @param SaleTicket $saleTicket
	 * @return SaleTicket
	 */
	public function save(SaleTicket $saleTicket): SaleTicket
	{
		if (!$saleTicket->save()) {
			throw new \RuntimeException($saleTicket->getErrorSummary(false)[0]);
		}
		return $saleTicket;
	}

	/**
	 * @param int $caseId
	 * @param int $saleId
	 * @return array|ActiveRecord[]
	 */
	public function findByPrimaryKeys(int $caseId, int $saleId)
	{
		$tickets = SaleTicket::find()->where(['st_case_id' => $caseId, 'st_case_sale_id' => $saleId])->all();
		if (!$tickets) {
			throw new NotFoundException('Sale Tickets are not found');
		}
		return $tickets;
	}
}