<?php
namespace sales\model\saleTicket\useCase\create;

use common\models\CaseSale;
use sales\helpers\cases\CaseSaleHelper;
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
			$isPassengerInfant = $this->isPassengerInfant((string)$rule['ticket_number'], $saleData['passengers']);
			$cntPassengers = CaseSaleHelper::getPassengersCountExceptInf($saleData['passengers']);
			$dto = (new SaleTicketCreateDTO())->feelBySaleData($caseSale->css_cs_id, $caseSale->css_sale_id, $saleData['pnr'] ?? '', $firstLastName, $isPassengerInfant, $cntPassengers, $penaltyTypeId, $rule, $refundRules);
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
	 * @return bool
	 * @throws \Throwable
	 */
	public function refreshSaleTicketBySaleData(int $caseId, CaseSale $caseSale, array $saleData): bool
	{
		$ci = $this;
		return $this->transactionManager->wrap(static function () use ($caseId, $caseSale, $saleData, &$ci) {
			$ci->saleTicketRepository->deleteByCaseAndSale($caseId, $caseSale->css_sale_id);
			return $ci->createSaleTicketBySaleData($caseSale, $saleData);
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
		$passenger = $this->getPassengerByTicketNumber($ticketNumber, $passengers);

		if ($passenger && $passenger['ticket_number'] && $passenger['ticket_number'] === $ticketNumber) {
			return trim($passenger['first_name'] . ' / ' . $passenger['last_name']);
		}
		return '';
	}

	/**
	 * @param string $ticketNumber
	 * @param array $passengers
	 * @return bool
	 */
	private function isPassengerInfant(string $ticketNumber, array $passengers): bool
	{
		$passenger = $this->getPassengerByTicketNumber($ticketNumber, $passengers);
		if ($passenger && $passenger['type'] === 'INF') {
			return true;
		}
		return false;
	}

	/**
	 * @param string $ticketNumber
	 * @param array $passengers
	 * @return array|null
	 */
	private function getPassengerByTicketNumber(string $ticketNumber, array $passengers): ?array
	{
		foreach ($passengers as $passenger) {
			if ($passenger['ticket_number'] === $ticketNumber) {
				return $passenger;
			}
		}
		return null;
	}
}