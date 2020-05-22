<?php

namespace sales\model\saleTicket\useCase\create;

use sales\model\saleTicket\entity\SaleTicket;
use sales\repositories\Repository;

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
}