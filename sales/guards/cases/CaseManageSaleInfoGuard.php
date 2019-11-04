<?php

namespace sales\guards\cases;

use common\models\CaseSale;
use common\models\Employee;
use Exception;
use sales\entities\cases\CasesStatus;
use sales\services\cases\CasesSaleService;

/**
 * Class CaseManageSaleInfoGuard
 * @package sales\guards\case
 *
 * @property CasesSaleService $casesSaleService
 */
class CaseManageSaleInfoGuard
{
	/**
	 * @var CasesSaleService
	 */
	private $casesSaleService;

	/**
	 * CaseManageSaleInfoGuard constructor.
	 * @param CasesSaleService $casesSaleService
	 */
	public function __construct(CasesSaleService $casesSaleService)
	{
		$this->casesSaleService = $casesSaleService;
	}

	/**
	 * @param CaseSale $caseSale
	 * @param Employee $user
	 * @param array $passengers
	 * @return string|null
	 */
	public function canManageSaleInfo(CaseSale $caseSale, Employee $user, array $passengers = []): ?string
	{
		try {

			if (
				!$user->isAdmin() &&
				!$user->isSuperAdmin() &&
				!$user->isExSuper() &&
				!$user->isSupSuper() &&
				!$caseSale->cssCs->isOwner($user->getId())
			) {
				throw new \DomainException('Sale info cannot be managed, reason: Access Denied');
			}

			if (!$caseSale->cssCs->isProcessing()) {
				throw new \DomainException('Sale info cannot be managed, reason: Case is not in status - ' . CasesStatus::getName(CasesStatus::STATUS_PROCESSING));
			}

			if (!$this->verifyPassengersAttributeNameref($passengers)) {
				throw new \DomainException('Sale info cannot be managed, reason: passenger dont have all the necessary attributes to synchronize with B\O');
			}

		} catch (\DomainException $e) {
			return $e->getMessage();
		}

		return null;
	}

	/**
	 * @param array $passengers
	 * @return bool
	 */
	private function verifyPassengersAttributeNameref(array $passengers): bool
	{
		return $this->casesSaleService->checkIfPassengersHasNamerefAttribute($passengers);
	}
}