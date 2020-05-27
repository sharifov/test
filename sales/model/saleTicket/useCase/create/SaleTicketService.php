<?php
namespace sales\model\saleTicket\useCase\create;

use sales\model\saleTicket\entity\SaleTicket;
use sales\services\TransactionManager;

/**
 * Class SaleTicketService
 * @package sales\model\saleTicket\useCase\create
 *
 * @property SaleTicketRepository $saleTicketRepository
 * @property TransactionManager $transactionManager
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

	public function __construct(SaleTicketRepository $saleTicketRepository, TransactionManager $transactionManager)
	{
		$this->saleTicketRepository = $saleTicketRepository;
		$this->transactionManager = $transactionManager;
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
			$firstLastName = $this->getPassengerName($rule , $saleData['passengers']);
			$cntPassengers = $this->getPassengersCountExceptInf($saleData['passengers']);
			$dto = (new SaleTicketCreateDTO())->feelBySaleData($caseId, $saleId, $saleData['pnr'] ?? '', $firstLastName, $cntPassengers, $rule, $refundRules);
			$saleTicket = SaleTicket::createBySaleData($dto);
			$this->saleTicketRepository->save($saleTicket);
		}
		return true;
	}

	/**
	 * @param int $caseId
	 * @param int $saleId
	 * @param array $saleData
	 * @return mixed
	 * @throws \Throwable
	 */
	public function refreshSaleTicketBySaleData(int $caseId, int $saleId, array $saleData)
	{
		$ci = $this;
		return $this->transactionManager->wrap(static function () use ($caseId, $saleId, $saleData, &$ci) {
			$ci->saleTicketRepository->deleteByCaseAndSale($caseId, $saleId);
			$ci->createSaleTicketBySaleData($caseId, $saleId, $saleData);
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