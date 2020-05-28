<?php
namespace sales\model\saleTicket\useCase\create;

use common\models\CaseSale;
use sales\model\saleTicket\entity\SaleTicket;
use sales\repositories\cases\CasesSaleRepository;
use sales\services\TransactionManager;

/**
 * Class SaleTicketService
 * @package sales\model\saleTicket\useCase\create
 *
 * @property SaleTicketRepository $saleTicketRepository
 * @property TransactionManager $transactionManager
 * @property CasesSaleRepository $casesSaleRepository
 */
class SaleTicketService
{
	/**
	 * @var SaleTicketRepository
	 */
	private $saleTicketRepository;

	/**
	 * @var TransactionManager
	 */
	private $transactionManager;
	/**
	 * @var CasesSaleRepository
	 */
	private $casesSaleRepository;

	public function __construct(SaleTicketRepository $saleTicketRepository, CasesSaleRepository $casesSaleRepository, TransactionManager $transactionManager)
	{
		$this->saleTicketRepository = $saleTicketRepository;
		$this->transactionManager = $transactionManager;
		$this->casesSaleRepository = $casesSaleRepository;
	}

	/**
	 * @param int $caseId
	 * @param CaseSale $caseSale
	 * @param array $saleData
	 * @return bool
	 */
	public function createSaleTicketBySaleData(CaseSale $caseSale, array $saleData): bool
	{
		if (empty($saleData['refundRules'])) {
			return false;
		}
		$refundRules = $saleData['refundRules'];
		$penaltyTypeId = SaleTicket::getPenaltyTypeId(trim($refundRules['airline_penalty'] ?? ''));
		foreach ($refundRules['rules'] as $rule) {
			$firstLastName = $this->getPassengerName($rule , $saleData['passengers']);
			$cntPassengers = $this->getPassengersCountExceptInf($saleData['passengers']);
			$dto = (new SaleTicketCreateDTO())->feelBySaleData($caseSale->css_cs_id, $caseSale->css_sale_id, $saleData['pnr'] ?? '', $firstLastName, $cntPassengers, $penaltyTypeId, $rule, $refundRules);
			$saleTicket = SaleTicket::createBySaleData($dto);
			$this->saleTicketRepository->save($saleTicket);
		}

		$departureDt = $this->casesSaleRepository->getFirstDepartureDtFromItinerary($saleData);
		$this->casesSaleRepository->setPenaltyTypeAndDepartureDt($penaltyTypeId, $departureDt, $caseSale);
		return true;
	}

	/**
	 * @param int $caseId
	 * @param CaseSale $caseSale
	 * @param array $saleData
	 * @return mixed
	 * @throws \Throwable
	 */
	public function refreshSaleTicketBySaleData(int $caseId, CaseSale $caseSale, array $saleData)
	{
		$ci = $this;
		return $this->transactionManager->wrap(static function () use ($caseId, $caseSale, $saleData, &$ci) {
			$ci->saleTicketRepository->deleteByCaseAndSale($caseId, $caseSale->css_sale_id);
			$ci->createSaleTicketBySaleData($caseSale, $saleData);
		});
	}

	private function getPassengerName(array $rule, array $passengers): string
	{
		if (!empty($rule['nameref'])) {
			return $this->getPassengerNameByNameref($rule['nameref'], $passengers);
		}

		if (!empty($rule['ticket_number'])) {
			return $this->getPassengerNameByTicketNumber($rule['ticket_number'], $passengers);
		}
		return '';
	}

	private function getPassengerNameByNameref(string $nameref, array $passengers): string
	{
		foreach ($passengers as $passenger) {
			if ($passenger['nameref'] === $nameref) {
				return trim($passenger['first_name'] . ' / ' . $passenger['last_name']);
			}
		}
		return '';
	}

	private function getPassengerNameByTicketNumber(string $ticketNumber, array $passengers): string
	{
		foreach ($passengers as $passenger) {
			if ($passenger['ticket_number'] === $ticketNumber) {
				return trim($passenger['first_name'] . ' / ' . $passenger['last_name']);
			}
		}
		return '';
	}

	private function getPassengersCountExceptInf(array $passengers): int
	{
		$cnt = 0;
		foreach ($passengers as $passenger) {
			if ($passenger['type'] !== 'INF') {
				$cnt++;
			}
		}
		return $cnt;
	}
}