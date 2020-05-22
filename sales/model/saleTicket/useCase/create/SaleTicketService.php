<?php
namespace sales\model\saleTicket\useCase\create;

use sales\model\saleTicket\entity\SaleTicket;

/**
 * Class SaleTicketService
 * @package sales\model\saleTicket\useCase\create
 *
 * @property SaleTicketRepository $saleTicketRepository
 */
class SaleTicketService
{
	private $saleTicketRepository;

	public function __construct(SaleTicketRepository $saleTicketRepository)
	{
		$this->saleTicketRepository = $saleTicketRepository;
	}

	/**
	 * @param int $caseId
	 * @param int $saleId
	 * @param array $saleData
	 * @return bool
	 */
	public function createSaleTicketBySaleData(int $caseId, int $saleId, array $saleData): bool
	{
		if (empty($saleData['refundRules'])) {
			return false;
		}
		$refundRules = $saleData['refundRules'];
		foreach ($refundRules['rules'] as $rule) {
			$dto = (new SaleTicketCreateDTO())->feelBySaleData($caseId, $saleId, $saleData['pnr'] ?? '', $rule, $refundRules, $saleData['customerInfo']);
			$saleTicket = SaleTicket::createBySaleData($dto);
			$this->saleTicketRepository->save($saleTicket);
		}
		return true;
	}
}